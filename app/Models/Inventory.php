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
        'quantity',
        'status_id'
    ];

    protected $casts = [
        'rental_price' => 'decimal:2',
        'quantity' => 'integer',
    ];
    public function status(){
        return $this->belongsTo(InventoryStatus::class, 'status_id', 'status_id');
    }
}
