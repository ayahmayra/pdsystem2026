<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RekeningBelanja extends Model
{
    protected $table = 'rekening_belanja';
    
    protected $fillable = [
        'sub_keg_id',
        'kode_rekening',
        'nama_rekening',
        'pagu',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'sub_keg_id' => 'integer',
        'pagu' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the sub kegiatan that owns the rekening belanja.
     */
    public function subKeg(): BelongsTo
    {
        return $this->belongsTo(SubKeg::class, 'sub_keg_id');
    }

    /**
     * Get the receipts that use this rekening belanja.
     */
    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class, 'rekening_belanja_id');
    }

    /**
     * Get the display name for the rekening belanja.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->kode_rekening . ' - ' . $this->nama_rekening;
    }

    /**
     * Get formatted pagu amount.
     */
    public function getFormattedPaguAttribute(): string
    {
        if ($this->pagu === null) {
            return 'Belum ditentukan';
        }
        
        return 'Rp ' . number_format($this->pagu, 0, ',', '.');
    }

    /**
     * Get total realisasi from receipts.
     */
    public function getTotalRealisasiAttribute(): float
    {
        return $this->receipts->sum(function ($receipt) {
            return $receipt->lines->sum('line_total');
        });
    }

    /**
     * Get formatted total realisasi.
     */
    public function getFormattedTotalRealisasiAttribute(): string
    {
        return 'Rp ' . number_format($this->total_realisasi, 0, ',', '.');
    }

    /**
     * Get sisa anggaran (pagu - realisasi).
     */
    public function getSisaAnggaranAttribute(): float
    {
        $pagu = $this->pagu ?? 0;
        return $pagu - $this->total_realisasi;
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

    /**
     * Scope a query to only include active rekening belanja.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
