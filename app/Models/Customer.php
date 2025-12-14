<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'contact_number',
        'address',
        'measurement',
        'status_id'
    ];

    protected $casts = [
        'measurement' => 'array',
    ];

    public function status()
    {
        return $this->belongsTo(CustomerStatus::class, 'status_id', 'status_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'customer_id', 'customer_id');
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'customer_id', 'customer_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_id', 'customer_id');
    }
}
