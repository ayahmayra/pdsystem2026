<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntraDistrictTransportRef extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_place_id',
        'destination_district_id',
        'pp_amount',
    ];

    protected $casts = [
        'pp_amount' => 'decimal:2',
    ];

    public function originPlace(): BelongsTo
    {
        return $this->belongsTo(OrgPlace::class, 'origin_place_id');
    }

    public function destinationDistrict(): BelongsTo
    {
        return $this->belongsTo(District::class, 'destination_district_id');
    }

    public function getDisplayNameAttribute(): string
    {
        $origin = $this->originPlace->name ?? 'Tempat Kerja Tidak Ditemukan';
        $destination = $this->destinationDistrict->name ?? 'Kecamatan Tidak Ditemukan';
        return "{$origin} → {$destination}";
    }

    public function getRouteDisplayAttribute(): string
    {
        $origin = $this->originPlace->name ?? 'Tempat Kerja Tidak Ditemukan';
        $destination = $this->destinationDistrict->name ?? 'Kecamatan Tidak Ditemukan';
        return "{$origin} → {$destination}";
    }
}
