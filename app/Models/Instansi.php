<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instansi extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'website',
    ];

    /**
     * Relationship with users
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get full name with code
     */
    public function fullName(): string
    {
        if ($this->code) {
            return "{$this->code} - {$this->name}";
        }
        return $this->name;
    }
}
