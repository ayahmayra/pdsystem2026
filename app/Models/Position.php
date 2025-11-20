<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    protected $fillable = [
        'name',
        'type',
        'echelon_id',
    ];

    /**
     * Relationship with users
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relationship with echelon
     */
    public function echelon(): BelongsTo
    {
        return $this->belongsTo(Echelon::class);
    }

    /**
     * Get full position name with echelon
     */
    public function fullName(): string
    {
        if ($this->echelon) {
            return $this->name . ' (' . $this->echelon->fullName() . ')';
        }
        
        return $this->name . ' (Non Eselon)';
    }

    /**
     * Get echelon display name
     */
    public function getEchelonDisplay(): string
    {
        return $this->echelon ? $this->echelon->fullName() : 'Non Eselon';
    }
}
