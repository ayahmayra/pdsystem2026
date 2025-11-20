<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripReportSigner extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_report_id', 'name', 'nip', 'position',
    ];

    public function tripReport() { return $this->belongsTo(TripReport::class); }
}
