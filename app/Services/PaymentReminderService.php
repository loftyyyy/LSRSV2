<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\RentalNotification;
use Carbon\Carbon;

class PaymentReminderService
{
    /**
     * Reminder types
     */
    public const REMINDER_TYPES = [
        'due_soon' => 'Payment Due Soon',
        'overdue' => 'Payment Overdue',
        'partial_payment' => 'Partial Payment Received',
        'payment_confirmed' => 'Payment Confirmed',
    ];

    /**
     * Get invoices with upcoming due dates
     *
     * @param  int  $daysAhead  Number of days to look ahead
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUpcomingDueInvoices(int $daysAhead = 3)
    {
        $today = Carbon::now()->startOfDay();
        $futureDate = Carbon::now()->addDays($daysAhead)->endOfDay();

        return Invoice::with(['customer', 'rental', 'reservation'])
            ->where('balance_due', '>', 0)
            ->whereBetween('due_date', [$today, $futureDate])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Get overdue invoices
     *
     * @param  int|null  $daysOverdue  Minimum days overdue (null for all overdue)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOverdueInvoices(?int $daysOverdue = null)
    {
        $query = Invoice::with(['customer', 'rental', 'reservation', 'payments'])
            ->where('balance_due', '>', 0)
            ->where('due_date', '<', Carbon::now()->startOfDay());

        if ($daysOverdue !== null) {
            $query->where('due_date', '<=', Carbon::now()->subDays($daysOverdue)->startOfDay());
        }

        return $query->orderBy('due_date', 'asc')->get();
    }

    /**
     * Create payment reminder notification
     */
    public function createPaymentReminder(
        Invoice $invoice,
        string $reminderType,
        ?string $customMessage = null
    ): RentalNotification {
        $title = self::REMINDER_TYPES[$reminderType] ?? 'Payment Reminder';
        $message = $customMessage ?? $this->generateReminderMessage($invoice, $reminderType);

        return RentalNotification::create([
            'type' => 'payment_reminder',
            'title' => $title,
            'message' => $message,
            'data' => json_encode([
                'invoice_id' => $invoice->invoice_id,
                'invoice_number' => $invoice->invoice_number,
                'customer_id' => $invoice->customer_id,
                'customer_name' => $invoice->customer
                    ? $invoice->customer->first_name.' '.$invoice->customer->last_name
                    : 'N/A',
                'amount_due' => $invoice->balance_due,
                'due_date' => $invoice->due_date?->format('Y-m-d'),
                'reminder_type' => $reminderType,
            ]),
            'is_read' => false,
            'created_at' => now(),
        ]);
    }

    /**
     * Send payment reminders for upcoming due invoices
     */
    public function sendUpcomingDueReminders(int $daysAhead = 3): array
    {
        $invoices = $this->getUpcomingDueInvoices($daysAhead);
        $reminders = [];

        foreach ($invoices as $invoice) {
            // Check if reminder was already sent today
            if (! $this->hasRecentReminder($invoice, 'due_soon', 1)) {
                $reminder = $this->createPaymentReminder($invoice, 'due_soon');
                $reminders[] = $reminder;
            }
        }

        return [
            'invoices_checked' => $invoices->count(),
            'reminders_sent' => count($reminders),
            'reminders' => $reminders,
        ];
    }

    /**
     * Send payment reminders for overdue invoices
     *
     * @param  int  $reminderInterval  Days between reminders
     */
    public function sendOverdueReminders(int $reminderInterval = 7): array
    {
        $invoices = $this->getOverdueInvoices();
        $reminders = [];

        foreach ($invoices as $invoice) {
            // Check if reminder was already sent within interval
            if (! $this->hasRecentReminder($invoice, 'overdue', $reminderInterval)) {
                $reminder = $this->createPaymentReminder($invoice, 'overdue');
                $reminders[] = $reminder;
            }
        }

        return [
            'invoices_checked' => $invoices->count(),
            'reminders_sent' => count($reminders),
            'reminders' => $reminders,
        ];
    }

    /**
     * Get payment reminder statistics
     */
    public function getReminderStatistics(): array
    {
        $today = Carbon::now()->startOfDay();
        $upcomingCount = Invoice::where('balance_due', '>', 0)
            ->whereBetween('due_date', [$today, $today->copy()->addDays(7)])
            ->count();

        $overdueCount = Invoice::where('balance_due', '>', 0)
            ->where('due_date', '<', $today)
            ->count();

        $totalOverdueAmount = (float) Invoice::where('balance_due', '>', 0)
            ->where('due_date', '<', $today)
            ->sum('balance_due');

        $remindersSentToday = RentalNotification::where('type', 'payment_reminder')
            ->whereDate('created_at', Carbon::today())
            ->count();

        // Group overdue by severity
        $overdueSeverity = [
            '1-7_days' => Invoice::where('balance_due', '>', 0)
                ->whereBetween('due_date', [$today->copy()->subDays(7), $today])
                ->count(),
            '8-14_days' => Invoice::where('balance_due', '>', 0)
                ->whereBetween('due_date', [$today->copy()->subDays(14), $today->copy()->subDays(7)])
                ->count(),
            '15-30_days' => Invoice::where('balance_due', '>', 0)
                ->whereBetween('due_date', [$today->copy()->subDays(30), $today->copy()->subDays(14)])
                ->count(),
            'over_30_days' => Invoice::where('balance_due', '>', 0)
                ->where('due_date', '<', $today->copy()->subDays(30))
                ->count(),
        ];

        return [
            'upcoming_due' => $upcomingCount,
            'total_overdue' => $overdueCount,
            'total_overdue_amount' => $totalOverdueAmount,
            'reminders_sent_today' => $remindersSentToday,
            'overdue_by_severity' => $overdueSeverity,
        ];
    }

    /**
     * Get customer payment history with reminders
     */
    public function getCustomerPaymentReminders(int $customerId): array
    {
        $customer = Customer::with([
            'invoices' => function ($query) {
                $query->orderBy('due_date', 'desc');
            },
            'invoices.payments',
        ])->findOrFail($customerId);

        $overdueInvoices = $customer->invoices->filter(function ($invoice) {
            return $invoice->balance_due > 0 &&
                   $invoice->due_date &&
                   Carbon::parse($invoice->due_date)->isPast();
        });

        $upcomingDueInvoices = $customer->invoices->filter(function ($invoice) {
            return $invoice->balance_due > 0 &&
                   $invoice->due_date &&
                   ! Carbon::parse($invoice->due_date)->isPast();
        });

        return [
            'customer' => [
                'id' => $customer->customer_id,
                'name' => $customer->first_name.' '.$customer->last_name,
                'email' => $customer->email,
                'contact_number' => $customer->contact_number,
            ],
            'summary' => [
                'total_invoices' => $customer->invoices->count(),
                'overdue_count' => $overdueInvoices->count(),
                'overdue_amount' => (float) $overdueInvoices->sum('balance_due'),
                'upcoming_due_count' => $upcomingDueInvoices->count(),
                'upcoming_due_amount' => (float) $upcomingDueInvoices->sum('balance_due'),
            ],
            'overdue_invoices' => $overdueInvoices->map(function ($invoice) {
                return [
                    'invoice_id' => $invoice->invoice_id,
                    'invoice_number' => $invoice->invoice_number,
                    'balance_due' => (float) $invoice->balance_due,
                    'due_date' => $invoice->due_date?->format('Y-m-d'),
                    'days_overdue' => Carbon::parse($invoice->due_date)->diffInDays(Carbon::now()),
                ];
            })->values(),
            'upcoming_due_invoices' => $upcomingDueInvoices->map(function ($invoice) {
                return [
                    'invoice_id' => $invoice->invoice_id,
                    'invoice_number' => $invoice->invoice_number,
                    'balance_due' => (float) $invoice->balance_due,
                    'due_date' => $invoice->due_date?->format('Y-m-d'),
                    'days_until_due' => Carbon::now()->diffInDays(Carbon::parse($invoice->due_date)),
                ];
            })->values(),
        ];
    }

    /**
     * Generate reminder message based on type
     */
    private function generateReminderMessage(Invoice $invoice, string $reminderType): string
    {
        $customerName = $invoice->customer
            ? $invoice->customer->first_name.' '.$invoice->customer->last_name
            : 'Customer';
        $amount = number_format($invoice->balance_due, 2);
        $invoiceNumber = $invoice->invoice_number;
        $dueDate = $invoice->due_date?->format('M d, Y') ?? 'N/A';

        switch ($reminderType) {
            case 'due_soon':
                return "Payment reminder for {$customerName}: Invoice #{$invoiceNumber} ".
                       "with balance of ₱{$amount} is due on {$dueDate}.";

            case 'overdue':
                $daysOverdue = $invoice->due_date
                    ? Carbon::parse($invoice->due_date)->diffInDays(Carbon::now())
                    : 0;

                return "OVERDUE: Invoice #{$invoiceNumber} for {$customerName} ".
                       "with balance of ₱{$amount} is {$daysOverdue} days overdue.";

            case 'partial_payment':
                return "Partial payment received for Invoice #{$invoiceNumber}. ".
                       "Remaining balance: ₱{$amount}.";

            case 'payment_confirmed':
                return "Payment confirmed for Invoice #{$invoiceNumber} from {$customerName}.";

            default:
                return "Payment reminder for Invoice #{$invoiceNumber}: ₱{$amount} due.";
        }
    }

    /**
     * Check if a reminder was recently sent for an invoice
     */
    private function hasRecentReminder(Invoice $invoice, string $reminderType, int $days): bool
    {
        return RentalNotification::where('type', 'payment_reminder')
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->where('data', 'like', '%"invoice_id":'.$invoice->invoice_id.'%')
            ->where('data', 'like', '%"reminder_type":"'.$reminderType.'"%')
            ->exists();
    }
}
