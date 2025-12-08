<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'invoice_id';

    protected $fillable = [
        'payment_id',
        'invoice_number',
        'invoice_date',
        'total_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'invoice_date' => 'datetime',
    ];

    public function payment(){
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }
}
