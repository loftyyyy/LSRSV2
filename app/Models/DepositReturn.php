<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositReturn extends Model
{
    use HasFactory;

    protected $table = 'deposit_returns';
    protected $primaryKey = 'return_id';

    protected $fillable = [
        'rental_id',
        'customer_id',
        'original_deposit_amount',
        'amount_returned',
        'amount_deducted',
        'deductions_breakdown',
        'return_method',
        'return_reference',
        'inspection_id',
        'status',
        'notes',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'original_deposit_amount' => 'decimal:2',
        'amount_returned' => 'decimal:2',
        'amount_deducted' => 'decimal:2',
        'deductions_breakdown' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the rental this deposit return belongs to
     */
    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }

    /**
     * Get the customer receiving the deposit return
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the staff who processed the return
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'user_id');
    }

    /**
     * Get total deductions as formatted array
     */
    public function getDeductionsAttribute(): array
    {
        return $this->deductions_breakdown ?? [];
    }

    /**
     * Calculate net return (what customer gets back)
     */
    public function getNetReturnAttribute(): float
    {
        return $this->amount_returned;
    }

    /**
     * Check if this is a full return
     */
    public function isFullReturn(): bool
    {
        return $this->amount_deducted == 0;
    }

    /**
     * Check if this is a partial return
     */
    public function isPartialReturn(): bool
    {
        return $this->amount_deducted > 0 && $this->amount_returned > 0;
    }

    /**
     * Check if entire deposit was forfeited
     */
    public function isForfeited(): bool
    {
        return $this->amount_returned == 0 && $this->amount_deducted > 0;
    }

    /**
     * Process the deposit return
     */
    public function process(): void
    {
        $this->update([
            'status' => 'processed',
            'processed_at' => now(),
        ]);

        // Update rental deposit status
        $rental = $this->rental;
        if ($rental) {
            $rental->update([
                'deposit_status' => $this->isForfeited() ? 'forfeited' : 
                                   ($this->isPartialReturn() ? 'returned_partial' : 'returned_full'),
                'deposit_returned_amount' => $this->amount_returned,
                'deposit_deducted_amount' => $this->amount_deducted,
            ]);
        }
    }
}
