<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'item_code',
        'item_name',
        'category_id',
        'size',
        'color',
        'rental_price',
        'deposit_amount',
        'item_status_id',
        'description',
        'image_path',
    ];

    protected $casts = [
        'rental_price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    /**
     * Get the category of the item
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Get the status of the item
     */
    public function itemStatus()
    {
        return $this->belongsTo(ItemStatus::class, 'item_status_id', 'status_id');
    }

    /**
     * Get all reservation items for this item
     */
    public function reservationItems()
    {
        return $this->hasMany(ReservationItem::class, 'item_id', 'item_id');
    }

    /**
     * Get all reservations that include this item
     */
    public function reservations()
    {
        return $this->hasManyThrough(
            Reservation::class,
            ReservationItem::class,
            'item_id',
            'reservation_id',
            'item_id',
            'reservation_id'
        );
    }

    /**
     * Get all rental items for this item
     */
    public function rentalItems()
    {
        return $this->hasMany(RentalItem::class, 'item_id', 'item_id');
    }

    /**
     * Scope to get only available items
     */
    public function scopeAvailable($query)
    {
        return $query->whereHas('itemStatus', function ($q) {
            $q->where('status_name', 'Available');
        });
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to search items
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('item_name', 'like', "%{$search}%")
                ->orWhere('item_code', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Check if item is available for a specific date range
     */
    public function isAvailableForDates($startDate, $endDate, $excludeReservationId = null)
    {
        $query = $this->reservationItems()
            ->whereHas('reservation', function ($resQuery) use ($startDate, $endDate, $excludeReservationId) {
                $resQuery->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($innerQ) use ($startDate, $endDate) {
                            $innerQ->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })
                    ->whereHas('status', function ($statusQ) {
                        $statusQ->where('status_name', '!=', 'Cancelled');
                    });

                if ($excludeReservationId) {
                    $resQuery->where('reservation_id', '!=', $excludeReservationId);
                }
            });

        return $query->count() === 0;
    }
}
