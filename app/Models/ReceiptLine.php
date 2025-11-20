<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_id', 'component', 'category', 'qty', 'unit', 'unit_amount', 'no_lodging', 'line_total', 
        'ref_table', 'ref_id', 'cap_amount', 'reference_rate_snapshot', 'destination_city_id', 'is_over_cap', 'over_cap_amount', 'remark', 'desc',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_amount' => 'decimal:2',
        'no_lodging' => 'boolean',
        'line_total' => 'decimal:2',
        'cap_amount' => 'decimal:2',
        'reference_rate_snapshot' => 'decimal:2',
        'is_over_cap' => 'boolean',
        'over_cap_amount' => 'decimal:2',
    ];

    public function receipt() { return $this->belongsTo(Receipt::class); }
    public function destinationCity() { return $this->belongsTo(City::class, 'destination_city_id'); }
}
