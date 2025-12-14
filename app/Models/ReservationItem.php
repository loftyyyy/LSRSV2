<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationItem extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationItemFactory> */
    use HasFactory;

    protected $table = 'reservation_items';
    protected $primaryKey = 'reservation_item_id';
    protected $fillable = [
        'reservation_id',
        'item_id',
        'fulfillment_status',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id', 'reservation_id');
    }

    public function item()
    {
        return $this->belongsTo(Inventory::class, 'item_id', 'item_id');
    }

}
