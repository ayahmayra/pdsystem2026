<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrgPlace extends Model
{
    protected $fillable = [
        'name',
        'city_id',
        'district_id',
        'is_org_headquarter',
    ];

    protected $casts = [
        'is_org_headquarter' => 'boolean',
    ];

    /**
     * Get city of this org place
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get district of this org place
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get full location info
     */
    public function getLocationInfoAttribute(): string
    {
        $location = [];
        
        if ($this->district) {
            $location[] = $this->district->name;
        }
        
        if ($this->city) {
            $location[] = $this->city->display_name;
        }
        
        return !empty($location) ? implode(', ', $location) : 'Lokasi tidak ditentukan';
    }

    /**
     * Get display name with headquarter indicator
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->name;
        
        if ($this->is_org_headquarter) {
            $name .= ' (Kantor Pusat)';
        }
        
        return $name;
    }
}
