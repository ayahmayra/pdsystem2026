<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LodgingCap extends Model
{
    protected $fillable = [
        'province_id',
        'travel_grade_id',
        'cap_amount',
    ];

    protected $casts = [
        'province_id' => 'integer',
        'travel_grade_id' => 'integer',
        'cap_amount' => 'decimal:2',
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
        return "{$this->province->name} - {$this->travelGrade->name}";
    }

    public function getFormattedCapAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->cap_amount, 0, ',', '.');
    }
}
