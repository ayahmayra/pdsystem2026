<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocNumberFormat extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_type', 'unit_scope_id', 'format_string', 'doc_code', 'reset_policy', 'padding', 'is_active', 'notes',
    ];

    public function unitScope() { return $this->belongsTo(Unit::class, 'unit_scope_id'); }
    public function sequences() { return $this->hasMany(NumberSequence::class, 'doc_type', 'doc_type'); }
}
