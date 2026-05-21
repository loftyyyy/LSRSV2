<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Service for logging security-relevant audit events
 *
 * Usage:
 *   AuditService::log('update_customer', $customer, ['email' => 'old@example.com']);
 *   AuditService::log('login_failed', changes: ['email' => 'user@example.com', 'ip' => '192.168.1.1']);
 */
class AuditService
{
    /**
     * Log an action for audit trail
     *
     * @param string $action The action being performed (create, update, delete, login, logout, etc.)
     * @param Model|null $model The model being acted upon
     * @param array|null $changes What changed (for updates, show old vs new values)
     * @return AuditLog
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?array $changes = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? $model::class : null,
            'model_id' => $model?->getKey(),
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }

    /**
     * Get audit history for a specific model
     */
    public static function getHistory(Model $model)
    {
        return AuditLog::where('model_type', $model::class)
            ->where('model_id', $model->getKey())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user's activity log
     */
    public static function getUserActivity($userId, $limit = 50)
    {
        return AuditLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get failed login attempts
     */
    public static function getFailedLogins($limit = 50)
    {
        return AuditLog::where('action', 'login_failed')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Detect suspicious activity (multiple failed logins from same IP)
     */
    public static function detectSuspiciousActivity($ipAddress, $threshold = 5)
    {
        $failedAttempts = AuditLog::where('action', 'login_failed')
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->count();

        return $failedAttempts >= $threshold;
    }
}
