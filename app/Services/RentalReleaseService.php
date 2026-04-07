<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\InventoryStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\RentalStatus;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\ReservationItemAllocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RentalReleaseService
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly DepositService $depositService,
    ) {}

    /**
     * Release an item to customer
     *
     * @param  array  $data  Release data with item_id, customer_id, dates, etc.
     * @param  int  $releasedBy  User ID
     * @return array|Rental
     */
    public function releaseItem(array $data, int $releasedBy)
    {
        return DB::transaction(function () use ($data, $releasedBy) {
            // Step 1: Load and validate the physical item
            $item = $this->getAndValidateItem($data['item_id']);

            if (isset($item['error'])) {
                return $item;
            }

            // Step 2: Load reservation if provided
            $reservation = $data['reservation_id']
                ? Reservation::find($data['reservation_id'])
                : null;

            // Step 2.5: Validate reservation status if provided
            if ($reservation) {
                $reservationValidation = $this->validateReservationForRelease($reservation, $data);
                if (isset($reservationValidation['error'])) {
                    return $reservationValidation;
                }
            }

            // Step 3: Determine deposit amount from item
            $depositAmount = $this->getDepositAmount($item);

            if (isset($depositAmount['error'])) {
                return $depositAmount;
            }

            // Step 4: Create Rental record
            $rental = $this->createRentalRecord($data, $item, $releasedBy, $depositAmount);

            // Step 5: Create Invoice with line items
            $invoice = $this->createInvoiceWithItems($rental, $item, $depositAmount);

            // Step 6: Collect deposit if requested
            if ($data['collect_deposit'] ?? true) {
                $depositPayment = $this->collectDeposit(
                    $rental,
                    $invoice,
                    $depositAmount,
                    $data['deposit_payment_method'] ?? 'cash',
                    $data['deposit_payment_notes'] ?? null,
                    $releasedBy
                );

                if (isset($depositPayment['error'])) {
                    throw new \RuntimeException($depositPayment['error']);
                }
            }

            // Step 7: Update item inventory status
            $this->updateItemStatus($item, 'rented');

            // Step 8: Handle reservation item allocation if applicable
            if ($reservation) {
                $this->handleReservationAllocation($rental, $reservation, $item, $releasedBy);
            }

            // Step 9: Log inventory movement
            $this->logInventoryMovement($item, 'release', $rental, $reservation, $data['release_notes'] ?? null);

            return $rental->load(['customer', 'item', 'item.variant', 'status', 'reservation', 'releasedBy']);
        });
    }

    /**
     * Validate reservation status and dates for item release
     *
     * @param  array  $data  Release data
     * @return array|null Error array if validation fails, null if valid
     */
    private function validateReservationForRelease(Reservation $reservation, array $data): ?array
    {
        // Guard 1: Reservation must be confirmed
        if (strtolower($reservation->status?->status_name ?? '') !== 'confirmed') {
            return [
                'error' => "Reservation #{$reservation->reservation_id} must be confirmed before releasing items. ".
                          'Current status: '.($reservation->status?->status_name ?? 'unknown').'. '.
                          'Please confirm the reservation first using the confirmReservation endpoint.',
                'code' => 422,
            ];
        }

        // Guard 2: Release date must be within reservation date range
        $releaseDate = Carbon::parse($data['released_date']);
        $reservationStartDate = Carbon::parse($reservation->start_date);
        $reservationEndDate = Carbon::parse($reservation->end_date);

        if ($releaseDate->isBefore($reservationStartDate)) {
            return [
                'error' => "Release date ({$releaseDate->format('Y-m-d')}) cannot be before reservation start date ({$reservationStartDate->format('Y-m-d')}). ".
                          'Items must be released within the reservation date range.',
                'code' => 422,
            ];
        }

        if ($releaseDate->isAfter($reservationEndDate)) {
            return [
                'error' => "Release date ({$releaseDate->format('Y-m-d')}) cannot be after reservation end date ({$reservationEndDate->format('Y-m-d')}). ".
                          'Items must be released within the reservation date range.',
                'code' => 422,
            ];
        }

        return null; // Validation passed
    }

    /**
     * Get and validate the physical item
     */
    private function getAndValidateItem(int $itemId): array|Inventory
    {
        $item = Inventory::with(['variant', 'status'])
            ->find($itemId);

        if (! $item) {
            return [
                'error' => "Physical item #{$itemId} not found in inventory.",
                'code' => 404,
            ];
        }

        // Check if item is available
        if ($item->status?->status_name !== 'available') {
            $currentStatus = $item->status?->status_name ?? 'unknown';

            return [
                'error' => "Item #{$item->sku} is currently {$currentStatus}. ".
                          'Only available items can be released.',
                'code' => 422,
            ];
        }

        // Check if item is already rented
        $activeRental = Rental::where('item_id', $itemId)
            ->whereNull('return_date')
            ->exists();

        if ($activeRental) {
            return [
                'error' => "Item #{$item->sku} is already currently rented to another customer.",
                'code' => 422,
            ];
        }

        // Validate item has variant
        if (! $item->variant_id) {
            return [
                'error' => "Item #{$item->sku} is not linked to any inventory variant.",
                'code' => 422,
            ];
        }

        return $item;
    }

    /**
     * Get deposit amount from item or variant
     */
    private function getDepositAmount(Inventory $item): array|float
    {
        // Try item's deposit first, then variant's
        $depositAmount = (float) $item->deposit_amount
            ?? (float) $item->variant?->deposit_amount
            ?? 0;

        if ($depositAmount <= 0) {
            return [
                'error' => "Item variant '{$item->variant?->name}' does not have a configured deposit amount. ".
                          'Please configure a deposit amount in the variant settings.',
                'code' => 422,
            ];
        }

        return $depositAmount;
    }

    /**
     * Create rental record
     */
    private function createRentalRecord(array $data, Inventory $item, int $releasedBy, float $depositAmount): Rental
    {
        $rentalStatus = RentalStatus::whereRaw('LOWER(status_name) = ?', ['rented'])
            ->firstOrFail();

        return Rental::create([
            'reservation_id' => $data['reservation_id'] ?? null,
            'item_id' => $item->item_id,
            'customer_id' => $data['customer_id'],
            'released_by' => $releasedBy,
            'released_date' => $data['released_date'],
            'due_date' => $data['due_date'],
            'original_due_date' => $data['due_date'],
            'status_id' => $rentalStatus->status_id,
            'extension_count' => 0,
            'deposit_amount' => $depositAmount,
            'deposit_status' => 'pending_collection',
        ]);
    }

    /**
     * Create invoice with line items
     */
    private function createInvoiceWithItems(
        Rental $rental,
        Inventory $item,
        float $depositAmount
    ): Invoice {
        $rentalFee = (float) $item->rental_price ?? 0;

        $invoice = Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'customer_id' => $rental->customer_id,
            'reservation_id' => $rental->reservation_id,
            'rental_id' => $rental->rental_id,
            'invoice_type' => 'rental',
            'invoice_date' => Carbon::now(),
            'due_date' => Carbon::parse($rental->due_date)->addDays(7),
            'subtotal' => $rentalFee + $depositAmount,
            'discount' => 0,
            'tax' => 0,
            'total_amount' => $rentalFee + $depositAmount,
            'amount_paid' => 0,
            'balance_due' => $rentalFee + $depositAmount,
            'created_by' => auth()->id() ?? $rental->released_by,
        ]);

        // Create rental fee line item
        InvoiceItem::create([
            'invoice_id' => $invoice->invoice_id,
            'description' => "Rental: {$item->name} ({$item->sku})",
            'item_type' => 'rental_fee',
            'item_id' => $item->item_id,
            'quantity' => 1,
            'unit_price' => $rentalFee,
            'total_price' => $rentalFee,
        ]);

        // Create deposit line item
        InvoiceItem::create([
            'invoice_id' => $invoice->invoice_id,
            'description' => "Security Deposit: {$item->name}",
            'item_type' => 'deposit',
            'item_id' => $item->item_id,
            'quantity' => 1,
            'unit_price' => $depositAmount,
            'total_price' => $depositAmount,
        ]);

        return $invoice;
    }

    /**
     * Collect deposit using PaymentService
     */
    private function collectDeposit(
        Rental $rental,
        Invoice $invoice,
        float $depositAmount,
        string $paymentMethod,
        ?string $notes,
        int $releasedBy
    ): array|Payment {
        try {
            $payment = $this->paymentService->processPayment([
                'invoice_id' => $invoice->invoice_id,
                'amount' => $depositAmount,
                'payment_method' => $paymentMethod,
                'notes' => $notes ?? 'Deposit collected during item release',
            ], $releasedBy);

            // Update rental deposit status
            $rental->update([
                'deposit_status' => 'held',
                'deposit_collected_by' => $releasedBy,
                'deposit_collected_at' => $payment->payment_date,
            ]);

            return $payment;
        } catch (\Exception $e) {
            return [
                'error' => "Failed to collect deposit: {$e->getMessage()}",
                'code' => 500,
            ];
        }
    }

    /**
     * Update item inventory status
     */
    private function updateItemStatus(Inventory $item, string $statusName): void
    {
        $status = InventoryStatus::whereRaw('LOWER(status_name) = ?', [strtolower($statusName)])
            ->firstOrFail();

        $item->update(['status_id' => $status->status_id]);
    }

    /**
     * Handle reservation item allocation
     */
    private function handleReservationAllocation(
        Rental $rental,
        Reservation $reservation,
        Inventory $item,
        int $releasedBy
    ): void {
        // Find reservation item for this variant
        $reservationItem = ReservationItem::where('reservation_id', $reservation->reservation_id)
            ->where('variant_id', $item->variant_id)
            ->first();

        if ($reservationItem) {
            // Update or create allocation
            $allocation = ReservationItemAllocation::firstOrNew([
                'reservation_item_id' => $reservationItem->reservation_item_id,
                'item_id' => $item->item_id,
            ]);

            $allocation->allocation_status = 'released';
            $allocation->allocated_at = $allocation->allocated_at ?? now();
            $allocation->released_at = now();
            $allocation->updated_by = $releasedBy;
            $allocation->save();

            // Update reservation item fulfillment
            $this->syncReservationItemFulfillment($reservationItem);

            // Update reservation status if all items released
            $this->updateReservationStatus($reservation);
        }
    }

    /**
     * Sync reservation item fulfillment status
     */
    private function syncReservationItemFulfillment(ReservationItem $reservationItem): void
    {
        $totalQuantity = $reservationItem->quantity;
        $releasedCount = ReservationItemAllocation::where('reservation_item_id', $reservationItem->reservation_item_id)
            ->where('allocation_status', 'released')
            ->where('returned_at', null)
            ->count();

        if ($releasedCount >= $totalQuantity) {
            $reservationItem->update(['fulfillment_status' => 'fulfilled']);
        } else {
            $reservationItem->update(['fulfillment_status' => 'partial']);
        }
    }

    /**
     * Update reservation status based on fulfillment
     */
    private function updateReservationStatus(Reservation $reservation): void
    {
        $allFulfilled = $reservation->items()
            ->where('fulfillment_status', '<>', 'fulfilled')
            ->doesntExist();

        if ($allFulfilled) {
            $completedStatus = \App\Models\ReservationStatus::whereRaw('LOWER(status_name) = ?', ['completed'])
                ->first();

            if ($completedStatus) {
                $reservation->update(['status_id' => $completedStatus->status_id]);
            }
        }
    }

    /**
     * Log inventory movement
     */
    private function logInventoryMovement(
        Inventory $item,
        string $movementType,
        Rental $rental,
        ?Reservation $reservation,
        ?string $notes
    ): void {
        $fromStatus = InventoryStatus::whereRaw('LOWER(status_name) = ?', ['available'])->first();
        $toStatus = InventoryStatus::whereRaw('LOWER(status_name) = ?', ['rented'])->first();

        InventoryMovement::create([
            'item_id' => $item->item_id,
            'movement_type' => $movementType,
            'from_status_id' => $fromStatus?->status_id,
            'to_status_id' => $toStatus?->status_id,
            'reference_type' => 'rental',
            'reference_id' => $rental->rental_id,
            'notes' => $notes ?? "Item released to customer: {$rental->customer->first_name} {$rental->customer->last_name}",
            'created_by' => auth()->id() ?? $rental->released_by,
        ]);
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV-'.Carbon::now()->format('Y');
        $lastInvoice = Invoice::where('invoice_number', 'like', "{$prefix}%")
            ->orderByRaw('CAST(SUBSTRING(invoice_number, -6) AS UNSIGNED) DESC')
            ->first();

        if ($lastInvoice && preg_match('/-(\d{6})$/', $lastInvoice->invoice_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return "{$prefix}-".str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
