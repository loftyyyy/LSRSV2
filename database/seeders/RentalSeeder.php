<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\InventoryStatus;
use App\Models\InventoryVariant;
use App\Models\Rental;
use App\Models\RentalStatus;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\ReservationItemAllocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = DB::table('users')->value('user_id');
        if (!$userId) {
            return;
        }

        $rentedStatusId = RentalStatus::whereRaw('LOWER(status_name) = ?', ['rented'])->value('status_id');
        $returnedStatusId = RentalStatus::whereRaw('LOWER(status_name) = ?', ['returned'])->value('status_id');
        $overdueStatusId = RentalStatus::whereRaw('LOWER(status_name) = ?', ['overdue'])->value('status_id');

        $inventoryAvailableStatusId = InventoryStatus::whereRaw('LOWER(status_name) = ?', ['available'])->value('status_id');
        $inventoryRentedStatusId = InventoryStatus::whereRaw('LOWER(status_name) = ?', ['rented'])->value('status_id');
        $inventoryReservedStatusId = InventoryStatus::whereRaw('LOWER(status_name) = ?', ['reserved'])->value('status_id');

        if (!$rentedStatusId || !$returnedStatusId || !$overdueStatusId || !$inventoryAvailableStatusId || !$inventoryRentedStatusId || !$inventoryReservedStatusId) {
            return;
        }

        $variantMap = InventoryVariant::pluck('variant_id', 'name');
        $reservations = Reservation::orderBy('start_date')->get()->values();
        if ($reservations->count() < 3) {
            return;
        }

        $this->seedActiveReservationRental(
            $reservations[0],
            $variantMap->get('Aurora Lace Bridal Gown'),
            $userId,
            $rentedStatusId,
            $inventoryAvailableStatusId,
            $inventoryRentedStatusId
        );

        $this->seedReturnedReservationRental(
            $reservations[2],
            $variantMap->get('Monarch Three-Piece Suit'),
            $userId,
            $rentedStatusId,
            $returnedStatusId,
            $inventoryAvailableStatusId,
            $inventoryRentedStatusId
        );

        $this->seedWalkInOverdueRental(
            $variantMap->get('Regal Wedding Barong Set'),
            $userId,
            $overdueStatusId,
            $inventoryAvailableStatusId,
            $inventoryRentedStatusId
        );

        $this->seedFutureReservationAllocations(
            $reservations[1],
            $variantMap->get('Sterling Classic Tuxedo'),
            $userId,
            $inventoryAvailableStatusId,
            $inventoryReservedStatusId
        );

        $this->refreshVariantCounters();
    }

    private function seedActiveReservationRental(
        Reservation $reservation,
        ?int $variantId,
        int $userId,
        int $rentedStatusId,
        int $inventoryAvailableStatusId,
        int $inventoryRentedStatusId
    ): void {
        if (!$variantId) {
            return;
        }

        DB::transaction(function () use ($reservation, $variantId, $userId, $rentedStatusId, $inventoryAvailableStatusId, $inventoryRentedStatusId) {
            $reservationItem = ReservationItem::where('reservation_id', $reservation->reservation_id)
                ->where('variant_id', $variantId)
                ->first();
            if (!$reservationItem) {
                return;
            }

            $item = Inventory::where('variant_id', $variantId)
                ->where('status_id', $inventoryAvailableStatusId)
                ->orderBy('item_id')
                ->first();
            if (!$item) {
                return;
            }

            $rental = Rental::updateOrCreate(
                [
                    'reservation_id' => $reservation->reservation_id,
                    'item_id' => $item->item_id,
                ],
                [
                    'customer_id' => $reservation->customer_id,
                    'released_by' => $userId,
                    'released_date' => now()->subDay()->toDateString(),
                    'due_date' => now()->addDay()->toDateString(),
                    'original_due_date' => now()->addDay()->toDateString(),
                    'status_id' => $rentedStatusId,
                    'extension_count' => 0,
                    'deposit_amount' => (float) $item->deposit_amount,
                    'deposit_status' => 'held',
                    'deposit_collected_by' => $userId,
                    'deposit_collected_at' => now()->subDay(),
                ]
            );

            $item->update(['status_id' => $inventoryRentedStatusId]);

            ReservationItemAllocation::updateOrCreate(
                [
                    'reservation_item_id' => $reservationItem->reservation_item_id,
                    'item_id' => $item->item_id,
                ],
                [
                    'allocation_status' => 'released',
                    'allocated_at' => now()->subDays(2),
                    'released_at' => now()->subDay(),
                    'updated_by' => $userId,
                ]
            );

            $reservationItem->update(['fulfillment_status' => 'fulfilled']);

            InventoryMovement::updateOrCreate(
                [
                    'item_id' => $item->item_id,
                    'movement_type' => 'release',
                    'rental_id' => $rental->rental_id,
                ],
                [
                    'variant_id' => $variantId,
                    'reservation_id' => $reservation->reservation_id,
                    'reservation_item_id' => $reservationItem->reservation_item_id,
                    'quantity' => 1,
                    'from_status_id' => $inventoryAvailableStatusId,
                    'to_status_id' => $inventoryRentedStatusId,
                    'performed_by' => $userId,
                    'notes' => 'Seeded release event for active rental',
                ]
            );
        });
    }

    private function seedReturnedReservationRental(
        Reservation $reservation,
        ?int $variantId,
        int $userId,
        int $rentedStatusId,
        int $returnedStatusId,
        int $inventoryAvailableStatusId,
        int $inventoryRentedStatusId
    ): void {
        if (!$variantId) {
            return;
        }

        DB::transaction(function () use ($reservation, $variantId, $userId, $rentedStatusId, $returnedStatusId, $inventoryAvailableStatusId, $inventoryRentedStatusId) {
            $reservationItem = ReservationItem::where('reservation_id', $reservation->reservation_id)
                ->where('variant_id', $variantId)
                ->first();
            if (!$reservationItem) {
                return;
            }

            $item = Inventory::where('variant_id', $variantId)->orderBy('item_id')->first();
            if (!$item) {
                return;
            }

            $rental = Rental::updateOrCreate(
                [
                    'reservation_id' => $reservation->reservation_id,
                    'item_id' => $item->item_id,
                ],
                [
                    'customer_id' => $reservation->customer_id,
                    'released_by' => $userId,
                    'released_date' => now()->subDays(8)->toDateString(),
                    'due_date' => now()->subDays(4)->toDateString(),
                    'original_due_date' => now()->subDays(4)->toDateString(),
                    'return_date' => now()->subDays(3)->toDateString(),
                    'returned_to' => $userId,
                    'return_notes' => 'Returned in good condition.',
                    'status_id' => $returnedStatusId,
                    'extension_count' => 1,
                    'extended_by' => $userId,
                    'last_extended_at' => now()->subDays(6),
                    'extension_reason' => 'Client requested one-day extension.',
                    'deposit_amount' => (float) $item->deposit_amount,
                    'deposit_status' => 'returned_full',
                    'deposit_returned_amount' => (float) $item->deposit_amount,
                    'deposit_collected_by' => $userId,
                    'deposit_collected_at' => now()->subDays(8),
                ]
            );

            $item->update(['status_id' => $inventoryAvailableStatusId]);

            ReservationItemAllocation::updateOrCreate(
                [
                    'reservation_item_id' => $reservationItem->reservation_item_id,
                    'item_id' => $item->item_id,
                ],
                [
                    'allocation_status' => 'returned',
                    'allocated_at' => now()->subDays(9),
                    'released_at' => now()->subDays(8),
                    'returned_at' => now()->subDays(3),
                    'updated_by' => $userId,
                ]
            );

            $reservationItem->update(['fulfillment_status' => 'fulfilled']);

            InventoryMovement::updateOrCreate(
                [
                    'item_id' => $item->item_id,
                    'movement_type' => 'release',
                    'rental_id' => $rental->rental_id,
                ],
                [
                    'variant_id' => $variantId,
                    'reservation_id' => $reservation->reservation_id,
                    'reservation_item_id' => $reservationItem->reservation_item_id,
                    'quantity' => 1,
                    'from_status_id' => $inventoryAvailableStatusId,
                    'to_status_id' => $inventoryRentedStatusId,
                    'performed_by' => $userId,
                    'notes' => 'Seeded release event for returned rental',
                ]
            );

            InventoryMovement::updateOrCreate(
                [
                    'item_id' => $item->item_id,
                    'movement_type' => 'return',
                    'rental_id' => $rental->rental_id,
                ],
                [
                    'variant_id' => $variantId,
                    'reservation_id' => $reservation->reservation_id,
                    'reservation_item_id' => $reservationItem->reservation_item_id,
                    'quantity' => 1,
                    'from_status_id' => $inventoryRentedStatusId,
                    'to_status_id' => $inventoryAvailableStatusId,
                    'performed_by' => $userId,
                    'notes' => 'Seeded return event for completed rental',
                ]
            );
        });
    }

    private function seedWalkInOverdueRental(
        ?int $variantId,
        int $userId,
        int $overdueStatusId,
        int $inventoryAvailableStatusId,
        int $inventoryRentedStatusId
    ): void {
        if (!$variantId) {
            return;
        }

        DB::transaction(function () use ($variantId, $userId, $overdueStatusId, $inventoryAvailableStatusId, $inventoryRentedStatusId) {
            $item = Inventory::where('variant_id', $variantId)
                ->where('status_id', $inventoryAvailableStatusId)
                ->orderBy('item_id')
                ->first();
            if (!$item) {
                return;
            }

            $customerId = DB::table('customers')->orderBy('customer_id')->value('customer_id');
            if (!$customerId) {
                return;
            }

            $rental = Rental::updateOrCreate(
                [
                    'reservation_id' => null,
                    'item_id' => $item->item_id,
                    'customer_id' => $customerId,
                ],
                [
                    'released_by' => $userId,
                    'released_date' => now()->subDays(6)->toDateString(),
                    'due_date' => now()->subDays(1)->toDateString(),
                    'original_due_date' => now()->subDays(1)->toDateString(),
                    'status_id' => $overdueStatusId,
                    'extension_count' => 0,
                    'deposit_amount' => (float) $item->deposit_amount,
                    'deposit_status' => 'held',
                    'deposit_collected_by' => $userId,
                    'deposit_collected_at' => now()->subDays(6),
                ]
            );

            $item->update(['status_id' => $inventoryRentedStatusId]);

            InventoryMovement::updateOrCreate(
                [
                    'item_id' => $item->item_id,
                    'movement_type' => 'release',
                    'rental_id' => $rental->rental_id,
                ],
                [
                    'variant_id' => $variantId,
                    'reservation_id' => null,
                    'reservation_item_id' => null,
                    'quantity' => 1,
                    'from_status_id' => $inventoryAvailableStatusId,
                    'to_status_id' => $inventoryRentedStatusId,
                    'performed_by' => $userId,
                    'notes' => 'Seeded walk-in overdue rental release event',
                ]
            );
        });
    }

    private function seedFutureReservationAllocations(
        Reservation $reservation,
        ?int $variantId,
        int $userId,
        int $inventoryAvailableStatusId,
        int $inventoryReservedStatusId
    ): void {
        if (!$variantId) {
            return;
        }

        DB::transaction(function () use ($reservation, $variantId, $userId, $inventoryAvailableStatusId, $inventoryReservedStatusId) {
            $reservationItem = ReservationItem::where('reservation_id', $reservation->reservation_id)
                ->where('variant_id', $variantId)
                ->first();

            if (!$reservationItem) {
                return;
            }

            $quantityToAllocate = max(1, min((int) $reservationItem->quantity, 2));
            $items = Inventory::where('variant_id', $variantId)
                ->where('status_id', $inventoryAvailableStatusId)
                ->orderBy('item_id')
                ->limit($quantityToAllocate)
                ->get();

            foreach ($items as $item) {
                $item->update(['status_id' => $inventoryReservedStatusId]);

                ReservationItemAllocation::updateOrCreate(
                    [
                        'reservation_item_id' => $reservationItem->reservation_item_id,
                        'item_id' => $item->item_id,
                    ],
                    [
                        'allocation_status' => 'allocated',
                        'allocated_at' => now()->subHours(6),
                        'released_at' => null,
                        'returned_at' => null,
                        'updated_by' => $userId,
                    ]
                );

                InventoryMovement::updateOrCreate(
                    [
                        'item_id' => $item->item_id,
                        'movement_type' => 'reserve',
                        'reservation_id' => $reservation->reservation_id,
                    ],
                    [
                        'variant_id' => $variantId,
                        'reservation_item_id' => $reservationItem->reservation_item_id,
                        'rental_id' => null,
                        'quantity' => 1,
                        'from_status_id' => $inventoryAvailableStatusId,
                        'to_status_id' => $inventoryReservedStatusId,
                        'performed_by' => $userId,
                        'notes' => 'Seeded reservation allocation before release',
                    ]
                );
            }

            $reservationItem->update(['fulfillment_status' => 'pending']);
        });
    }

    private function refreshVariantCounters(): void
    {
        $availableStatusId = InventoryStatus::whereRaw('LOWER(status_name) = ?', ['available'])->value('status_id');
        if (!$availableStatusId) {
            return;
        }

        $variants = InventoryVariant::all();
        foreach ($variants as $variant) {
            $variant->update([
                'total_units' => Inventory::where('variant_id', $variant->variant_id)->count(),
                'available_units' => Inventory::where('variant_id', $variant->variant_id)
                    ->where('status_id', $availableStatusId)
                    ->count(),
            ]);
        }
    }
}
