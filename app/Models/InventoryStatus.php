<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryStatus extends Model
{
    /** @use HasFactory<\Database\Factories\InventoryStatusFactory> */
    use HasFactory;
    protected $table = 'inventory_statuses';
    protected $primaryKey = 'status_id';
    protected $fillable = [
        'status_name'
    ];

}
