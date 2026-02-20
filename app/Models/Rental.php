<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    /** @use HasFactory<\Database\Factories\RentalFactory> */
    use HasFactory;

    protected $table = 'rentals';
    protected $primaryKey = 'rental_id';
    protected $fillable = [
        'reservation_id',
        'item_id',
        'customer_id',
        'released_by',
        'released_date',
        'due_date',
        'original_due_date',
        'extension_count',
        'extended_by',
        'last_extended_at',
        'extension_reason',
        'return_date',
        'return_notes',
        'returned_to',
        'status_id',
        // Deposit tracking
        'deposit_amount',
        'deposit_status',
        'deposit_returned_amount',
        'deposit_deducted_amount',
        'deposit_collected_by',
        'deposit_collected_at',
    ];

    protected $casts = [
        'released_date' => 'date',
        'due_date' => 'date',
        'original_due_date' => 'date',
        'return_date' => 'date',
        'extension_count' => 'integer',
        'last_extended_at' => 'datetime',
        // Deposit tracking
        'deposit_amount' => 'decimal:2',
        'deposit_returned_amount' => 'decimal:2',
        'deposit_deducted_amount' => 'decimal:2',
        'deposit_collected_at' => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id', 'reservation_id');
    }

    public function item()
    {
        return $this->belongsTo(Inventory::class, 'item_id', 'item_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function releasedBy()
    {
        return $this->belongsTo(User::class, 'released_by', 'user_id');
    }

    public function returnedTo()
    {
        return $this->belongsTo(User::class, 'returned_to', 'user_id');
    }

    public function extendedBy()
    {
        return $this->belongsTo(User::class, 'extended_by', 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(RentalStatus::class, 'status_id', 'status_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'rental_id', 'rental_id');
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class, 'rental_id', 'rental_id');
    }

    /**
     * Get the staff who collected the deposit
     */
    public function depositCollectedBy()
    {
        return $this->belongsTo(User::class, 'deposit_collected_by', 'user_id');
    }

    /**
     * Get all deposit return records for this rental
     */
    public function depositReturns()
    {
        return $this->hasMany(DepositReturn::class, 'rental_id', 'rental_id');
    }

    /**
     * Check if deposit is currently held
     */
    public function hasDepositHeld(): bool
    {
        return $this->deposit_status === 'held' && $this->deposit_amount > 0;
    }

    /**
     * Check if deposit has been returned (full or partial)
     */
    public function isDepositReturned(): bool
    {
        return in_array($this->deposit_status, ['returned_full', 'returned_partial']);
    }

    /**
     * Calculate remaining deposit to be returned
     */
    public function getRemainingDepositAttribute(): float
    {
        if (!$this->hasDepositHeld()) {
            return 0;
        }
        return $this->deposit_amount - $this->deposit_returned_amount - $this->deposit_deducted_amount;
    }

    /**
     * Scope to get rentals with held deposits
     */
    public function scopeWithHeldDeposit($query)
    {
        return $query->where('deposit_status', 'held')->where('deposit_amount', '>', 0);
    }

    /**
     * Scope to get rentals pending deposit return
     */
    public function scopePendingDepositReturn($query)
    {
        return $query->where('deposit_status', 'held')
            ->whereNotNull('return_date')
            ->where('deposit_amount', '>', 0);
    }

}
