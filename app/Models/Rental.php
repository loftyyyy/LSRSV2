<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    /** @use HasFactory<\Database\Factories\RentalFactory> */
    use HasFactory;

    protected $table = 'rentals';
    protected $primaryKey = 'rental_id';
    protected $fillable = [
        'reservation_id',
        'item_id',
        'customer_id',
        'released_by',
        'released_date',
        'due_date',
        'original_due_date',
        'extension_count',
        'extended_by',
        'last_extended_at',
        'extension_reason',
        'return_date',
        'return_notes',
        'returned_to',
        'status_id',
    ];

    protected $casts = [
        'released_date' => 'date',
        'due_date' => 'date',
        'original_due_date' => 'date',
        'return_date' => 'date',
        'extension_count' => 'integer',
        'last_extended_at' => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id', 'reservation_id');
    }

    public function item()
    {
        return $this->belongsTo(Inventory::class, 'item_id', 'item_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function releasedBy()
    {
        return $this->belongsTo(User::class, 'released_by', 'user_id');
    }

    public function returnedTo()
    {
        return $this->belongsTo(User::class, 'returned_to', 'user_id');
    }

    public function extendedBy()
    {
        return $this->belongsTo(User::class, 'extended_by', 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(RentalStatus::class, 'status_id', 'status_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'rental_id', 'rental_id');
    }

}
