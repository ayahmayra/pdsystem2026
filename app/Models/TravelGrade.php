<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TravelGrade extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    protected $casts = [
        'code' => 'string',
        'name' => 'string',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function perdiemRates(): HasMany
    {
        return $this->hasMany(PerdiemRate::class);
    }

    public function lodgingCaps(): HasMany
    {
        return $this->hasMany(LodgingCap::class);
    }

    public function representationRate(): HasOne
    {
        return $this->hasOne(RepresentationRate::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->code})";
    }
}
