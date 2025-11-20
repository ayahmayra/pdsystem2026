<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtCostComponent extends Model
{
    use HasFactory;

    protected $table = 'atcost_components';

    protected $fillable = [
        'code',
        'name',
    ];

    public function getDisplayNameAttribute(): string
    {
        return "{$this->code} - {$this->name}";
    }
}
