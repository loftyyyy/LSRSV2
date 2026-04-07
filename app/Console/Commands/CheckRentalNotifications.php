<?php

namespace App\Console\Commands;

use App\Models\Rental;
use App\Models\RentalNotification;
use App\Models\RentalSetting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckRentalNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rentals:check-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for due and overdue rentals and create notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking rental notifications...');

        // Get notification settings
        $reminderDays = (int) RentalSetting::getValue('notification_due_days_before', 2);
        $overdueEnabled = (bool) RentalSetting::getValue('notification_overdue_enabled', true);

        $createdCount = 0;

        // Check for rentals due within the reminder window
        $createdCount += $this->createDueReminders($reminderDays);

        // Check for overdue rentals
        if ($overdueEnabled) {
            $createdCount += $this->createOverdueAlerts();
        }

        $this->info("Created {$createdCount} notification(s).");

        return Command::SUCCESS;
    }

    /**
     * Create due reminder notifications
     */
    private function createDueReminders(int $reminderDays): int
    {
        $today = Carbon::now()->startOfDay();
        $reminderDate = $today->copy()->addDays($reminderDays);

        // Get active rentals due within the reminder window
        $rentals = Rental::with(['customer', 'item'])
            ->whereNull('return_date')
            ->whereBetween('due_date', [$today, $reminderDate])
            ->get();

        $count = 0;

        foreach ($rentals as $rental) {
            $dueDate = Carbon::parse($rental->due_date)->startOfDay();
            $daysUntilDue = $today->diffInDays($dueDate, false);

            // Skip if notification already exists for this rental today
            if (RentalNotification::existsForRentalToday($rental->rental_id, RentalNotification::TYPE_DUE_REMINDER)) {
                continue;
            }

            // Only create reminder for specific day thresholds (to avoid spam)
            if ($daysUntilDue > 0 && ($daysUntilDue === $reminderDays || $daysUntilDue === 1)) {
                try {
                    RentalNotification::createDueReminder($rental, $daysUntilDue);
                    $count++;
                    $this->line("  Due reminder created for rental #{$rental->rental_id} (due in {$daysUntilDue} days)");
                } catch (\Exception $e) {
                    Log::error("Failed to create due reminder for rental #{$rental->rental_id}: ".$e->getMessage());
                }
            }
        }

        return $count;
    }

    /**
     * Create overdue alert notifications
     */
    private function createOverdueAlerts(): int
    {
        $today = Carbon::now()->startOfDay();

        // Get penalty rate for calculations
        $penaltyRate = (float) RentalSetting::getValue('penalty_rate_per_day', 50);

        // Get overdue rentals
        $rentals = Rental::with(['customer', 'item'])
            ->whereNull('return_date')
            ->where('due_date', '<', $today)
            ->get();

        $count = 0;

        foreach ($rentals as $rental) {
            $dueDate = Carbon::parse($rental->due_date)->startOfDay();
            $daysOverdue = $dueDate->diffInDays($today);

            // Skip if notification already exists for this rental today
            if (RentalNotification::existsForRentalToday($rental->rental_id, RentalNotification::TYPE_OVERDUE_ALERT)) {
                continue;
            }

            // Calculate current penalty
            $penaltyAmount = $daysOverdue * $penaltyRate;

            try {
                RentalNotification::createOverdueAlert($rental, $daysOverdue, $penaltyAmount);
                $count++;
                $this->line("  Overdue alert created for rental #{$rental->rental_id} ({$daysOverdue} days overdue)");
            } catch (\Exception $e) {
                Log::error("Failed to create overdue alert for rental #{$rental->rental_id}: ".$e->getMessage());
            }
        }

        return $count;
    }
}
