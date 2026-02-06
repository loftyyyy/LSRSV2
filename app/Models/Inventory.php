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
        'sku',
        'item_type',
        'name',
        'size',
        'color',
        'design',
        'rental_price',
        'selling_price',
        'deposit_amount',
        'status_id',
        'updated_by'
    ];

    protected $casts = [
        'rental_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    public function status()
    {
        return $this->belongsTo(InventoryStatus::class, 'status_id', 'status_id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
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

    public function images()
    {
        return $this->hasMany(InventoryImage::class, 'item_id', 'item_id');
    }

    /**
     * Boot the model and auto-generate SKU if not provided
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inventory) {
            if (empty($inventory->sku)) {
                $inventory->sku = static::generateSku($inventory->item_type);
            }
        });
    }

    /**
     * Generate a unique SKU based on item type
     * Format: GWN-001, GWN-002 for gowns, SUT-001, SUT-002 for suits
     */
    protected static function generateSku(string $itemType): string
    {
        $prefix = strtoupper(substr($itemType, 0, 3)); // GWN or SUT

        // Get the highest number for this item type
        $lastItem = static::where('item_type', $itemType)
            ->where('sku', 'like', "{$prefix}-%")
            ->orderByRaw('CAST(SUBSTRING(sku, 5) AS UNSIGNED) DESC')
            ->first();

        if ($lastItem && preg_match('/-(\d+)$/', $lastItem->sku, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('%s-%03d', $prefix, $nextNumber);
    }

    /**
     * Check if item is available for sale
     */
    public function isAvailableForSale(): bool
    {
        return $this->selling_price !== null 
            && $this->selling_price > 0 
            && $this->status?->status_name === 'available';
    }

    /**
     * Check if item is sold
     */
    public function isSold(): bool
    {
        return $this->status?->status_name === 'sold';
    }

    /**
     * Scope to get items available for sale
     */
    public function scopeAvailableForSale($query)
    {
        return $query->whereNotNull('selling_price')
            ->where('selling_price', '>', 0)
            ->whereHas('status', function ($q) {
                $q->where('status_name', 'available');
            });
    }
}
