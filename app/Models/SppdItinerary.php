<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppdItinerary extends Model
{
    use HasFactory;

    protected $fillable = [
        'sppd_id', 'leg_no', 'date', 'from_place', 'to_place', 'mode_detail', 'ticket_no', 'remarks',
    ];

    public function sppd() { return $this->belongsTo(Sppd::class); }
}
