<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalStatus extends Model
{
    /** @use HasFactory<\Database\Factories\RentalStatusFactory> */
    use HasFactory;
    protected $table = 'rental_statuses';
    protected $primaryKey = 'status_id';

    protected $fillable = [
        "status_name"
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'status_id', 'status_id');
    }
}
