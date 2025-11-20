<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelRoute extends Model
{
    protected $fillable = [
        'origin_place_id',
        'destination_place_id',
        'mode_id',
        'is_roundtrip',
        'class',
    ];

    protected $casts = [
        'is_roundtrip' => 'boolean',
        'class' => 'string',
    ];

    public function originPlace(): BelongsTo
    {
        return $this->belongsTo(OrgPlace::class, 'origin_place_id');
    }

    public function destinationPlace(): BelongsTo
    {
        return $this->belongsTo(OrgPlace::class, 'destination_place_id');
    }

    public function transportMode(): BelongsTo
    {
        return $this->belongsTo(TransportMode::class, 'mode_id');
    }

    public function getRouteDescriptionAttribute(): string
    {
        $origin = $this->originPlace->display_name;
        $destination = $this->destinationPlace->display_name;
        $mode = $this->transportMode->name;
        $class = $this->class ? " ({$this->class})" : '';
        $roundtrip = $this->is_roundtrip ? ' (PP)' : '';
        
        return "{$origin} → {$destination} via {$mode}{$class}{$roundtrip}";
    }

    public function getShortRouteAttribute(): string
    {
        $origin = $this->originPlace->name;
        $destination = $this->destinationPlace->name;
        $mode = $this->transportMode->code;
        $class = $this->class ? " ({$this->class})" : '';
        $roundtrip = $this->is_roundtrip ? ' PP' : '';
        
        return "{$origin} → {$destination} ({$mode}){$class}{$roundtrip}";
    }
}
