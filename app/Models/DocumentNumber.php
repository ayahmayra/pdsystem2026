<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentNumber extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'doc_type', 'doc_id', 'number', 'generated_by_user_id', 'is_manual', 'old_number', 'format_id', 'sequence_id', 'meta', 'created_at',
    ];

    public function generatedByUser() { return $this->belongsTo(User::class, 'generated_by_user_id'); }
    public function format() { return $this->belongsTo(DocNumberFormat::class, 'format_id'); }
    public function sequence() { return $this->belongsTo(NumberSequence::class, 'sequence_id'); }
}
