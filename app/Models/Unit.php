<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = [
        'code',
        'name',
        'parent_id',
    ];

    /**
     * Relationship with parent unit
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'parent_id');
    }

    /**
     * Relationship with child units
     */
    public function children(): HasMany
    {
        return $this->hasMany(Unit::class, 'parent_id');
    }

    /**
     * Relationship with users
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relationship with sub kegiatan
     */
    public function subKegiatan(): HasMany
    {
        return $this->hasMany(SubKeg::class, 'id_unit');
    }

    /**
     * Get full name with code
     */
    public function fullName(): string
    {
        return "{$this->code} - {$this->name}";
    }
}
