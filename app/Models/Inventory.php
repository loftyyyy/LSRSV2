<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    /** @use HasFactory<\Database\Factories\InventoryFactory> */
    use HasFactory;
    protected $table = 'inventories';
    protected $primaryKey = 'item_id';
    protected $fillable = [
        'item_type',
        'name',
        'size',
        'color',
        'design',
        'condition',
        'rental_price',
        'status_id'
    ];

    protected $casts = [
        'rental_price' => 'decimal:2',
    ];

    public function status()
    {
        return $this->belongsTo(InventoryStatus::class, 'status_id', 'status_id');
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'item_id', 'item_id');
    }

    public function reservationItems()
    {
        return $this->hasMany(ReservationItem::class, 'item_id', 'item_id');
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'item_id', 'item_id');
    }
}
