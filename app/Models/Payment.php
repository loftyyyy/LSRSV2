<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'rental_id',
        'reservation_id',
        'payment_type',
        'payment_method',
        'payment_date',
        'processed_by',
        'status_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function rental(){
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }
    public function reservation(){
        return $this->belongsTo(Reservation::class, 'reservation_id', 'reservation_id');
    }
    public function processedBy(){
        return $this->belongsTo(User::class, 'processed_by', 'user_id');
    }
    public function status(){
        return $this->belongsTo(PaymentStatus::class, 'status_id', 'status_id');
    }
    public function invoice(){
        return $this->hasOne(Invoice::class, 'payment_id', 'payment_id');
    }
}
