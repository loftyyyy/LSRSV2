<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'invoice_id',
        'payment_reference',
        'amount',
        'payment_method',
        'payment_date',
        'notes',
        'processed_by',
        'status_id',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(PaymentStatus::class, 'status_id', 'status_id');
    }
}
