<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'invoice_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'invoice_id',
        'description',
        'item_type',
        'item_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    public function item()
    {
        return $this->belongsTo(Inventory::class, 'item_id', 'item_id');
    }
     /**
     * Check if this is a penalty/late fee
     */
    public function isPenalty(): bool
    {
        return in_array($this->item_type, ['penalty', 'late_fee']);
    }

    /**
     * Automatically calculate total_price before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($invoiceItem) {
            $invoiceItem->total_price = $invoiceItem->quantity * $invoiceItem->unit_price;
        });
    }
}
