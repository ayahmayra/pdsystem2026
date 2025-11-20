<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepresentationRate extends Model
{
    protected $fillable = [
        'travel_grade_id',
        'satuan',
        'luar_kota',
        'dalam_kota_gt8h',
    ];

    protected $casts = [
        'travel_grade_id' => 'integer',
        'satuan' => 'string',
        'luar_kota' => 'decimal:2',
        'dalam_kota_gt8h' => 'decimal:2',
    ];

    public function travelGrade(): BelongsTo
    {
        return $this->belongsTo(TravelGrade::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return "Representasi - {$this->travelGrade->name}";
    }

    public function getFormattedLuarKotaAttribute(): string
    {
        return 'Rp ' . number_format($this->luar_kota, 0, ',', '.');
    }

    public function getFormattedDalamKotaGt8hAttribute(): string
    {
        return 'Rp ' . number_format($this->dalam_kota_gt8h, 0, ',', '.');
    }
}
