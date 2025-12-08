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
}
