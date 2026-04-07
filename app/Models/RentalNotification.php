<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalNotification extends Model
{
    use HasFactory;

    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'rental_id',
        'customer_id',
        'type',
        'title',
        'message',
        'priority',
        'is_read',
        'is_dismissed',
        'read_at',
        'dismissed_at',
        'metadata',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_dismissed' => 'boolean',
        'read_at' => 'datetime',
        'dismissed_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Notification types
    public const TYPE_DUE_REMINDER = 'due_reminder';

    public const TYPE_OVERDUE_ALERT = 'overdue_alert';

    public const TYPE_RETURN_CONFIRMATION = 'return_confirmation';

    public const TYPE_EXTENSION_REMINDER = 'extension_reminder';

    public const TYPE_DEPOSIT_PENDING = 'deposit_pending';

    // Priority levels
    public const PRIORITY_LOW = 'low';

    public const PRIORITY_NORMAL = 'normal';

    public const PRIORITY_HIGH = 'high';

    public const PRIORITY_URGENT = 'urgent';

    /**
     * Get the rental associated with this notification
     */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }

    /**
     * Get the customer associated with this notification
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for active (not dismissed) notifications
     */
    public function scopeActive($query)
    {
        return $query->where('is_dismissed', false);
    }

    /**
     * Scope by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope by priority
     */
    public function scopeWithPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark notification as dismissed
     */
    public function dismiss(): bool
    {
        return $this->update([
            'is_dismissed' => true,
            'dismissed_at' => now(),
        ]);
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_URGENT => 'rose',
            self::PRIORITY_HIGH => 'amber',
            self::PRIORITY_NORMAL => 'sky',
            self::PRIORITY_LOW => 'neutral',
            default => 'neutral',
        };
    }

    /**
     * Get icon name based on notification type
     */
    public function getIconAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_DUE_REMINDER => 'calendar-clock',
            self::TYPE_OVERDUE_ALERT => 'alert-triangle',
            self::TYPE_RETURN_CONFIRMATION => 'check-circle',
            self::TYPE_EXTENSION_REMINDER => 'calendar-plus',
            self::TYPE_DEPOSIT_PENDING => 'wallet',
            default => 'bell',
        };
    }

    /**
     * Create a due reminder notification
     */
    public static function createDueReminder(Rental $rental, int $daysUntilDue): self
    {
        $priority = $daysUntilDue <= 1 ? self::PRIORITY_HIGH : self::PRIORITY_NORMAL;
        $dayText = $daysUntilDue === 1 ? 'tomorrow' : "in {$daysUntilDue} days";

        return self::create([
            'rental_id' => $rental->rental_id,
            'customer_id' => $rental->customer_id,
            'type' => self::TYPE_DUE_REMINDER,
            'title' => 'Rental Due Soon',
            'message' => "Rental for {$rental->item->item_name} by {$rental->customer->first_name} {$rental->customer->last_name} is due {$dayText}.",
            'priority' => $priority,
            'metadata' => [
                'days_until_due' => $daysUntilDue,
                'due_date' => $rental->due_date,
                'item_name' => $rental->item->item_name,
                'customer_name' => "{$rental->customer->first_name} {$rental->customer->last_name}",
            ],
        ]);
    }

    /**
     * Create an overdue alert notification
     */
    public static function createOverdueAlert(Rental $rental, int $daysOverdue, float $penaltyAmount = 0): self
    {
        $priority = $daysOverdue >= 7 ? self::PRIORITY_URGENT : ($daysOverdue >= 3 ? self::PRIORITY_HIGH : self::PRIORITY_NORMAL);

        return self::create([
            'rental_id' => $rental->rental_id,
            'customer_id' => $rental->customer_id,
            'type' => self::TYPE_OVERDUE_ALERT,
            'title' => 'Rental Overdue',
            'message' => "Rental for {$rental->item->item_name} is {$daysOverdue} day(s) overdue. Current penalty: ₱".number_format($penaltyAmount, 2),
            'priority' => $priority,
            'metadata' => [
                'days_overdue' => $daysOverdue,
                'due_date' => $rental->due_date,
                'penalty_amount' => $penaltyAmount,
                'item_name' => $rental->item->item_name,
                'customer_name' => "{$rental->customer->first_name} {$rental->customer->last_name}",
            ],
        ]);
    }

    /**
     * Check if a similar notification already exists today
     */
    public static function existsForRentalToday(int $rentalId, string $type): bool
    {
        return self::where('rental_id', $rentalId)
            ->where('type', $type)
            ->whereDate('created_at', today())
            ->exists();
    }
}
