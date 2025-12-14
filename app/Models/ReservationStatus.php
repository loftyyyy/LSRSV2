<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationStatus extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationStatusFactory> */
    use HasFactory;
    protected $table = 'reservation_statuses';
    protected $primaryKey = 'status_id';
    protected $fillable = [
        'status_name',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'status_id', 'status_id');
    }
}
