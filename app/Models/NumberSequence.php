<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumberSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_type', 'unit_scope_id', 'year_scope', 'month_scope', 'current_value', 'last_generated_at',
    ];

    public function unitScope() { return $this->belongsTo(Unit::class, 'unit_scope_id'); }
    public function format() { return $this->belongsTo(DocNumberFormat::class, 'doc_type', 'doc_type'); }
}
