<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalExtension extends Model
{
    protected $table = 'rental_extensions';

    protected $primaryKey = 'extension_id';

    protected $fillable = [
        'rental_id',
        'old_due_date',
        'new_due_date',
        'extension_reason',
        'extended_by',
    ];

    protected $casts = [
        'old_due_date' => 'date',
        'new_due_date' => 'date',
    ];

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }

    public function extendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'extended_by', 'user_id');
    }

    /**
     * Mutator: Ensure old_due_date is stored as Y-m-d format without time
     */
    public function setOldDueDateAttribute($value)
    {
        if ($value) {
            $this->attributes['old_due_date'] = \Carbon\Carbon::parse($value)->format('Y-m-d');
        }
    }

    /**
     * Mutator: Ensure new_due_date is stored as Y-m-d format without time
     */
    public function setNewDueDateAttribute($value)
    {
        if ($value) {
            $this->attributes['new_due_date'] = \Carbon\Carbon::parse($value)->format('Y-m-d');
        }
    }
}
