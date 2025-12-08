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
        'rental_id',
        'reservation_id',
        'released_by',
        'released_date',
        'penalty_fee',
        'due_date',
        'return_date',
        'penalty_fee',
        'status_id',
    ];

    protected $casts = [
        'released_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'penalty_fee' => 'decimal:2',
    ];

        public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id', 'reservation_id');
    }

    public function releasedBy()
    {
        return $this->belongsTo(User::class, 'released_by', 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(RentalStatus::class, 'status_id', 'status_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'rental_id', 'rental_id');
    }

}
