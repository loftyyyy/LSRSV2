<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationItemAllocation extends Model
{
    use HasFactory;

    protected $table = 'reservation_item_allocations';

    protected $fillable = [
        'reservation_item_id',
        'item_id',
        'allocation_status',
        'allocated_at',
        'released_at',
        'returned_at',
        'updated_by',
    ];

    protected $casts = [
        'allocated_at' => 'datetime',
        'released_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function reservationItem()
    {
        return $this->belongsTo(ReservationItem::class, 'reservation_item_id', 'reservation_item_id');
    }

    public function item()
    {
        return $this->belongsTo(Inventory::class, 'item_id', 'item_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }
}
