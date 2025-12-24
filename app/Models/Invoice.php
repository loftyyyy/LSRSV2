<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';
    protected $primaryKey = 'invoice_id';

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'reservation_id',
        'rental_id',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'amount_paid',
        'balance_due',
        'invoice_date',
        'due_date',
        'invoice_type',
        'payment_status',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'invoice_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id', 'reservation_id');
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id', 'invoice_id');
    }

    /**
     * Get total penalties from this invoice @return float
     */
    public function getTotalPenalties(): float
    {
        return $this->items()->whereIn('item_type', ['penalty, late_fee'])->sum('total_price');
    }
     /**
     * Get rental fee items
     */
    public function getRentalFees()
    {
        return $this->invoiceItems()
            ->where('item_type', 'rental_fee')
            ->get();
    }

    /**
     * Check if invoice is fully paid
     */
//    public function isPaid(): bool
//    {
//        return $this-> === 'paid';
//    }

}
