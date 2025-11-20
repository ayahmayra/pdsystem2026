<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AirfareRef extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_city_id',
        'destination_city_id',
        'class',
        'pp_estimate',
    ];

    protected $casts = [
        'origin_city_id' => 'integer',
        'destination_city_id' => 'integer',
        'class' => 'string',
        'pp_estimate' => 'decimal:2',
    ];

    public function originCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'origin_city_id');
    }

    public function destinationCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'destination_city_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->originCity->name} → {$this->destinationCity->name} ({$this->class})";
    }

    public function getFormattedPpEstimateAttribute(): string
    {
        return 'Rp ' . number_format($this->pp_estimate, 0, ',', '.');
    }

    public function getRouteDisplayAttribute(): string
    {
        return "{$this->originCity->name} → {$this->destinationCity->name}";
    }
}
