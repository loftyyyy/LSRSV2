<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'reservations';

    protected $primaryKey = 'reservation_id';

    protected $fillable = [
        'reserved_by',
        'customer_id',
        'status_id',
        'reservation_date',
        'start_date',
        'end_date',
        'confirmed_at',
        'confirmed_by',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'expiry_date',
        'expiry_checked_at',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'expiry_date' => 'date',
        'expiry_checked_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function reservedBy()
    {
        return $this->belongsTo(User::class, 'reserved_by', 'user_id');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by', 'user_id');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by', 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function status()
    {
        return $this->belongsTo(ReservationStatus::class, 'status_id', 'status_id');
    }

    public function items()
    {
        return $this->hasMany(ReservationItem::class, 'reservation_id', 'reservation_id');
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'reservation_id', 'reservation_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'reservation_id', 'reservation_id');
    }
}
