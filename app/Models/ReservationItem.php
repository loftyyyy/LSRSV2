<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationItem extends Model
{
    use HasFactory;

    protected $table = 'reservation_items';
    protected $primaryKey = 'reservation_item_id';

    protected $fillable = [
        'reservation_id',
        'item_id',
        'variant_id',
        'quantity',
        'rental_price',
        'fulfillment_status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'rental_price' => 'decimal:2',
    ];

    /**
     * Get the reservation that owns the reservation item
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id', 'reservation_id');
    }

    /**
     * Get the item details (from inventories table)
     */
    public function item()
    {
        return $this->belongsTo(Inventory::class, 'item_id', 'item_id');
    }

    public function variant()
    {
        return $this->belongsTo(InventoryVariant::class, 'variant_id', 'variant_id');
    }

    public function allocations()
    {
        return $this->hasMany(ReservationItemAllocation::class, 'reservation_item_id', 'reservation_item_id');
    }

    /**
     * Calculate subtotal for this reservation item
     */
    public function getSubtotalAttribute()
    {
        return $this->rental_price * $this->quantity;
    }

    /**
     * Calculate total rental days from reservation
     */
    public function getRentalDaysAttribute()
    {
        if (!$this->reservation) {
            return 0;
        }

        return $this->reservation->start_date->diffInDays($this->reservation->end_date) + 1;
    }

    /**
     * Calculate total cost (rental_price * quantity * days)
     */
    public function getTotalCostAttribute()
    {
        return $this->subtotal * $this->rental_days;
    }
}
