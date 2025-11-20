<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerdiemRate extends Model
{
    protected $fillable = [
        'province_id',
        'travel_grade_id',
        'satuan',
        'luar_kota',
        'dalam_kota_gt8h',
        'diklat',
    ];

    protected $casts = [
        'province_id' => 'integer',
        'travel_grade_id' => 'integer',
        'satuan' => 'string',
        'luar_kota' => 'decimal:2',
        'dalam_kota_gt8h' => 'decimal:2',
        'diklat' => 'decimal:2',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function travelGrade(): BelongsTo
    {
        return $this->belongsTo(TravelGrade::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->province->name}";
    }

    public function getFormattedLuarKotaAttribute(): string
    {
        return 'Rp ' . number_format($this->luar_kota, 0, ',', '.');
    }

    public function getFormattedDalamKotaGt8hAttribute(): string
    {
        return 'Rp ' . number_format($this->dalam_kota_gt8h, 0, ',', '.');
    }

    public function getFormattedDiklatAttribute(): string
    {
        return 'Rp ' . number_format($this->diklat, 0, ',', '.');
    }
}
