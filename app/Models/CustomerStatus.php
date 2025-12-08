<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerStatus extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerStatusFactory> */
    use HasFactory;

    protected $table = 'customer_status';
    protected $primaryKey = 'status_id';
    protected $fillable = [
        'status_id',
        'status_name',
        'reason'
    ];
}
