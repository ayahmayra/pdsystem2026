<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubKeg extends Model
{
    protected $table = 'sub_keg';
    
    protected $fillable = [
        'kode_subkeg',
        'nama_subkeg',
        'id_unit',
        'pptk_user_id',
    ];

    protected $casts = [
        'id_unit' => 'integer',
        'pptk_user_id' => 'integer',
    ];

    /**
     * Get the unit that owns the sub kegiatan.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'id_unit');
    }

    /**
     * Get the PPTK user that manages this sub kegiatan.
     */
    public function pptkUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pptk_user_id');
    }

    /**
     * Get the SPPD records that belong to this sub kegiatan.
     */
    public function sppd(): HasMany
    {
        return $this->hasMany(Sppd::class, 'sub_keg_id');
    }

    /**
     * Get the rekening belanja records that belong to this sub kegiatan.
     */
    public function rekeningBelanja(): HasMany
    {
        return $this->hasMany(RekeningBelanja::class, 'sub_keg_id');
    }

    /**
     * Get the active rekening belanja records that belong to this sub kegiatan.
     */
    public function activeRekeningBelanja(): HasMany
    {
        return $this->hasMany(RekeningBelanja::class, 'sub_keg_id')->where('is_active', true);
    }

    /**
     * Get the display name for the sub kegiatan.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->kode_subkeg . ' - ' . $this->nama_subkeg;
    }

    /**
     * Get the total pagu from active rekening belanja.
     */
    public function getTotalPaguAttribute(): float
    {
        return $this->activeRekeningBelanja->sum('pagu');
    }

    /**
     * Get formatted total pagu.
     */
    public function getFormattedTotalPaguAttribute(): string
    {
        return 'Rp ' . number_format($this->total_pagu, 0, ',', '.');
    }

    /**
     * Get the count of active rekening belanja.
     */
    public function getJumlahRekeningAttribute(): int
    {
        return $this->activeRekeningBelanja->count();
    }

    /**
     * Get total realisasi from all active rekening belanja.
     */
    public function getTotalRealisasiAttribute(): float
    {
        return $this->activeRekeningBelanja->sum(function ($rekening) {
            return $rekening->receipts->sum(function ($receipt) {
                return $receipt->lines->sum('line_total');
            });
        });
    }

    /**
     * Get sisa anggaran (total pagu - total realisasi).
     */
    public function getSisaAnggaranAttribute(): float
    {
        return $this->total_pagu - $this->total_realisasi;
    }

    /**
     * Get formatted total realisasi.
     */
    public function getFormattedTotalRealisasiAttribute(): string
    {
        return 'Rp ' . number_format($this->total_realisasi, 0, ',', '.');
    }

    /**
     * Get formatted sisa anggaran.
     */
    public function getFormattedSisaAnggaranAttribute(): string
    {
        $sisa = $this->sisa_anggaran;
        
        if ($sisa > 0) {
            return 'Rp ' . number_format($sisa, 0, ',', '.');
        } elseif ($sisa < 0) {
            return '-Rp ' . number_format(abs($sisa), 0, ',', '.');
        } else {
            return 'Rp 0';
        }
    }

}
