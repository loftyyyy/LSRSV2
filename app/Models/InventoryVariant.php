<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryVariant extends Model
{
    use HasFactory;

    protected $table = 'inventory_variants';
    protected $primaryKey = 'variant_id';

    protected $fillable = [
        'variant_sku',
        'item_type',
        'name',
        'size',
        'color',
        'design',
        'rental_price',
        'deposit_amount',
        'is_sellable',
        'selling_price',
        'total_units',
        'available_units',
    ];

    protected $casts = [
        'rental_price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'is_sellable' => 'boolean',
        'selling_price' => 'decimal:2',
        'total_units' => 'integer',
        'available_units' => 'integer',
    ];

    public function units()
    {
        return $this->hasMany(Inventory::class, 'variant_id', 'variant_id');
    }
}
