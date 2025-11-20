<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = [
        'kemendagri_code',
        'province_id',
        'name',
        'type',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    /**
     * Get province of this city
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Get districts in this city
     */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    /**
     * Get org places in this city
     */
    public function orgPlaces(): HasMany
    {
        return $this->hasMany(OrgPlace::class);
    }

    /**
     * Get airfare references where this city is origin
     */
    public function originAirfares(): HasMany
    {
        return $this->hasMany(AirfareRef::class, 'origin_city_id');
    }

    /**
     * Get airfare references where this city is destination
     */
    public function destinationAirfares(): HasMany
    {
        return $this->hasMany(AirfareRef::class, 'destination_city_id');
    }

    /**
     * Get full name with type and code
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->type} {$this->name} ({$this->kemendagri_code})";
    }

    /**
     * Get display name with type
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->type} {$this->name}";
    }
}
