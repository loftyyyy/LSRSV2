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
        'quantity',
        'penalty_fee',
    ];

}
