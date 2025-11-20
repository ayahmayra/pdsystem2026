<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SptMember extends Model
{
    use HasFactory;

    protected $table = 'spt_members';

    protected $fillable = [
        'spt_id',
        'user_id',
    ];

    public function spt() { return $this->belongsTo(Spt::class); }
    public function user() { return $this->belongsTo(User::class); }
}
