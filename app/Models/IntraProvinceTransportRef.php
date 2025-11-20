<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntraProvinceTransportRef extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_place_id',
        'destination_city_id',
        'pp_amount',
        'valid_from',
        'valid_to',
        'source_ref',
    ];

    protected $casts = [
        'pp_amount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function originPlace(): BelongsTo
    {
        return $this->belongsTo(OrgPlace::class, 'origin_place_id');
    }

    public function destinationCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'destination_city_id');
    }

    public function getDisplayNameAttribute(): string
    {
        $origin = $this->originPlace->name ?? 'Tempat Kerja Tidak Ditemukan';
        $destination = $this->destinationCity->name ?? 'Kota Tidak Ditemukan';
        return "{$origin} → {$destination}";
    }

    public function getRouteDisplayAttribute(): string
    {
        $origin = $this->originPlace->name ?? 'Tempat Kerja Tidak Ditemukan';
        $destination = $this->destinationCity->name ?? 'Kota Tidak Ditemukan';
        return "{$origin} → {$destination}";
    }
}
