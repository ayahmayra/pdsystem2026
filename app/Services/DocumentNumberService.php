<?php

namespace App\Services;

use App\Models\DocNumberFormat;
use App\Models\NumberSequence;
use App\Models\DocumentNumber;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    /**
     * Generate nomor dokumen otomatis.
     * @param string $docType (ND/SPT/SPPD/KWT/LAP)
     * @param int|null $unitScopeId
     * @param string|\DateTimeInterface|null $date
     * @param array $meta
     * @param int|null $userId
     * @return array ['number' => string, 'sequence' => NumberSequence, 'format' => DocNumberFormat]
     */
    public static function generate($docType, $unitScopeId = null, $date = null, $meta = [], $userId = null)
    {
        return DB::transaction(function () use ($docType, $unitScopeId, $date, $meta, $userId) {
            $date = $date ? Carbon::parse($date) : now();
            // 1. Cari format aktif
            $format = DocNumberFormat::where('doc_type', $docType)
                ->where(function($q) use ($unitScopeId) {
                    $q->where('unit_scope_id', $unitScopeId)->orWhereNull('unit_scope_id');
                })
                ->where('is_active', true)
                ->orderByRaw('unit_scope_id is null') // prioritas: scope > global
                ->first();
            if (!$format) {
                throw new \Exception('Format penomoran tidak ditemukan untuk tipe dokumen ini.');
            }
            // 2. Tentukan scope sequence
            $year = $date->year;
            $month = $date->month;
            $seqQuery = NumberSequence::where('doc_type', $docType)
                ->where('unit_scope_id', $unitScopeId);
            if ($format->reset_policy === 'YEARLY' || $format->reset_policy === 'MONTHLY') {
                $seqQuery->where('year_scope', $year);
            } else {
                $seqQuery->whereNull('year_scope');
            }
            if ($format->reset_policy === 'MONTHLY') {
                $seqQuery->where('month_scope', $month);
            } else {
                $seqQuery->whereNull('month_scope');
            }
            // 3. Ambil atau buat sequence (terkunci selama transaksi)
            $sequence = $seqQuery->lockForUpdate()->first();
            if (!$sequence) {
                $sequence = NumberSequence::create([
                    'doc_type' => $docType,
                    'unit_scope_id' => $unitScopeId,
                    'year_scope' => $format->reset_policy === 'YEARLY' || $format->reset_policy === 'MONTHLY' ? $year : null,
                    'month_scope' => $format->reset_policy === 'MONTHLY' ? $month : null,
                    'current_value' => 0,
                    'last_generated_at' => now(),
                ]);
            }
            $seqValue = $sequence->current_value + 1;
            // 4. Build nomor dan pastikan unik sesuai scope dokumen
            $maxRetry = 50;
            $retry = 0;
            do {
                $number = self::buildNumber($format, $seqValue, $unitScopeId, $date);
                
                // Scope penomoran:
                // - ND, SPT, LAP: GLOBAL (unique untuk seluruh OPD)
                // - SPPD, KWT: PER UNIT (unique per unit/bidang)
                if (in_array($docType, ['SPPD', 'KWT'])) {
                    // SPPD & KWT: cek unik per unit
                    if ($docType === 'SPPD') {
                        $exists = \App\Models\Sppd::where('doc_no', $number)
                            ->where('number_scope_unit_id', $unitScopeId)
                            ->lockForUpdate()
                            ->exists();
                    } else { // KWT (Receipts)
                        $exists = \App\Models\Receipt::where('doc_no', $number)
                            ->where('number_scope_unit_id', $unitScopeId)
                            ->lockForUpdate()
                            ->exists();
                    }
                } else {
                    // ND, SPT, LAP: cek unik global
                    $exists = DocumentNumber::where('number', $number)->lockForUpdate()->exists();
                }
                
                if ($exists) {
                    $seqValue++;
                    $retry++;
                }
            } while ($exists && $retry < $maxRetry);

            // 5. Update sequence dan simpan audit trail dalam transaksi yang sama
            $sequence->update([
                'current_value' => $seqValue,
                'last_generated_at' => now(),
            ]);
            
            // Audit trail: hanya untuk dokumen global scope (ND, SPT, LAP)
            $audit = null;
            if (!in_array($docType, ['SPPD', 'KWT'])) {
                $audit = DocumentNumber::create([
                    'doc_type' => $docType,
                    'doc_id' => $meta['doc_id'] ?? null,
                    'number' => $number,
                    'generated_by_user_id' => $userId,
                    'is_manual' => false,
                    'old_number' => null,
                    'format_id' => $format->id,
                    'sequence_id' => $sequence->id,
                    'meta' => json_encode($meta),
                    'created_at' => now(),
                ]);
            }
            
            return [
                'number' => $number,
                'sequence' => $sequence,
                'format' => $format,
                'audit' => $audit,
            ];
        });
    }

    /**
     * Override nomor dokumen secara manual (audit).
     */
    public static function override($docType, $docId, $manualNumber, $reason, $userId, $meta = [])
    {
        $audit = DocumentNumber::create([
            'doc_type' => $docType,
            'doc_id' => $docId,
            'number' => $manualNumber,
            'generated_by_user_id' => $userId,
            'is_manual' => true,
            'old_number' => $meta['old_number'] ?? null,
            'format_id' => $meta['format_id'] ?? null,
            'sequence_id' => $meta['sequence_id'] ?? null,
            'meta' => json_encode(array_merge($meta, ['manual_reason' => $reason])),
            'created_at' => now(),
        ]);
        return $audit;
    }

    /**
     * Build nomor dokumen dari format string.
     */
    protected static function buildNumber($format, $seqValue, $unitScopeId, $date)
    {
        $unitCode = null;
        if ($unitScopeId) {
            $unit = \App\Models\Unit::find($unitScopeId);
            $unitCode = $unit ? ($unit->code ?? $unit->id) : $unitScopeId;
        }
        $romanMonth = self::romanMonth($date->month);
        $replace = [
            '{seq}' => str_pad($seqValue, $format->padding, '0', STR_PAD_LEFT),
            '{doc_code}' => $format->doc_code,
            '{unit_code}' => $unitCode,
            '{roman_month}' => $romanMonth,
            '{month}' => $date->format('m'),
            '{year}' => $date->format('Y'),
        ];
        $number = $format->format_string;
        foreach ($replace as $key => $val) {
            $number = str_replace($key, $val, $number);
        }
        return $number;
    }

    public static function romanMonth($month)
    {
        $romans = [null, 'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        return $romans[(int)$month] ?? $month;
    }
}
