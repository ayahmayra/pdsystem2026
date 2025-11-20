<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistrictPerdiemRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'district_id',
        'travel_grade_id',
        'perdiem_rate',
    ];

    protected $casts = [
        'perdiem_rate' => 'decimal:2',
    ];

    /**
     * Get the district that owns the perdiem rate.
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the travel grade that owns the perdiem rate.
     */
    public function travelGrade()
    {
        return $this->belongsTo(TravelGrade::class);
    }

    /**
     * Get the display name attribute.
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->district->name} - {$this->travelGrade->name}";
    }

    /**
     * Get the formatted perdiem rate attribute.
     */
    public function getFormattedPerdiemRateAttribute()
    {
        return 'Rp ' . number_format($this->perdiem_rate, 0, ',', '.');
    }
}
