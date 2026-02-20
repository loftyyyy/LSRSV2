<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $table = 'inventory_movements';
    protected $primaryKey = 'movement_id';

    protected $fillable = [
        'item_id',
        'variant_id',
        'reservation_id',
        'reservation_item_id',
        'rental_id',
        'movement_type',
        'quantity',
        'from_status_id',
        'to_status_id',
        'performed_by',
        'notes',
    ];

    public function item()
    {
        return $this->belongsTo(Inventory::class, 'item_id', 'item_id');
    }

    public function variant()
    {
        return $this->belongsTo(InventoryVariant::class, 'variant_id', 'variant_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id', 'reservation_id');
    }

    public function reservationItem()
    {
        return $this->belongsTo(ReservationItem::class, 'reservation_item_id', 'reservation_item_id');
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }

    public function fromStatus()
    {
        return $this->belongsTo(InventoryStatus::class, 'from_status_id', 'status_id');
    }

    public function toStatus()
    {
        return $this->belongsTo(InventoryStatus::class, 'to_status_id', 'status_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by', 'user_id');
    }
}
