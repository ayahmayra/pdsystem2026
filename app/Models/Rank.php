<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rank extends Model
{
    protected $fillable = [
        'code',
        'name',
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
        return "{$this->code} - {$this->name}";
    }
}
