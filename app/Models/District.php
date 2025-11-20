<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $fillable = [
        'kemendagri_code',
        'city_id',
        'name',
    ];

    /**
     * Get city of this district
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get org places in this district
     */
    public function orgPlaces(): HasMany
    {
        return $this->hasMany(OrgPlace::class);
    }

    /**
     * Get full name with code
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} ({$this->kemendagri_code})";
    }
}
