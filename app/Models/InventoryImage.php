<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryImage extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'image_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'image_path',
        'image_url',
        'view_type',
        'caption',
        'is_primary',
        'display_order',
        'file_size',
        'mime_type'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
        'display_order' => 'integer',
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the inventory item that owns the image.
     */
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'item_id', 'item_id');
    }

    /**
     * Scope a query to only include primary images.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope a query to filter by view type.
     */
    public function scopeByViewType($query, string $viewType)
    {
        return $query->where('view_type', $viewType);
    }

    /**
     * Scope a query to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }

    /**
     * Get the file size in a human-readable format.
     */
    public function getFileSizeHumanAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->file_size;
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the view type label.
     */
    public function getViewTypeLabelAttribute(): string
    {
        return match($this->view_type) {
            'front' => 'Front View',
            'back' => 'Back View',
            'side' => 'Side View',
            'detail' => 'Detail View',
            'full' => 'Full View',
            default => 'Unknown View'
        };
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // When setting an image as primary, unset other primary images for the same item
        static::saving(function ($image) {
            if ($image->is_primary && $image->isDirty('is_primary')) {
                static::where('item_id', $image->item_id)
                    ->where('image_id', '!=', $image->image_id)
                    ->update(['is_primary' => false]);
            }
        });
    }
}
