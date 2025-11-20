<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppdDivisumSignoff extends Model
{
    use HasFactory;

    protected $fillable = [
        'sppd_id', 'signed_place', 'signed_date', 'signed_by_name', 'signed_by_position', 'note',
    ];

    public function sppd() { return $this->belongsTo(Sppd::class); }
}
