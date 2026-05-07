<?php

namespace Database\Seeders;

use App\Models\InventoryVariant;
use App\Models\Reservation;
use App\Models\ReservationItem;
use Illuminate\Database\Seeder;

class ReservationItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reservations = Reservation::orderBy('start_date')->get()->values();
        if ($reservations->count() < 5) {
            return;
        }

        $variantMap = InventoryVariant::pluck('variant_id', 'name');

        $lineItems = [
            [
                'reservation_id' => $reservations[0]->reservation_id,
                'variant_name' => 'Aurora Lace Bridal Gown',
                'quantity' => 1,
                'fulfillment_status' => 'pending',
                'notes' => 'Primary wedding gown fitting complete.',
            ],
            [
                'reservation_id' => $reservations[1]->reservation_id,
                'variant_name' => 'Sterling Classic Tuxedo',
                'quantity' => 2,
                'fulfillment_status' => 'pending',
                'notes' => 'For groom and best man.',
            ],
            [
                'reservation_id' => $reservations[2]->reservation_id,
                'variant_name' => 'Selene Satin Ball Gown',
                'quantity' => 1,
                'fulfillment_status' => 'pending',
                'notes' => 'Client requested veil matching add-on.',
            ],
            [
                'reservation_id' => $reservations[2]->reservation_id,
                'variant_name' => 'Monarch Three-Piece Suit',
                'quantity' => 1,
                'fulfillment_status' => 'pending',
                'notes' => 'Partner outfit bundle.',
            ],
            [
                'reservation_id' => $reservations[3]->reservation_id,
                'variant_name' => 'Crown Prince Kids Suit',
                'quantity' => 1,
                'fulfillment_status' => 'cancelled',
                'notes' => 'Reservation cancelled by customer.',
            ],
            [
                'reservation_id' => $reservations[4]->reservation_id,
                'variant_name' => 'Celeste Off-Shoulder Evening Gown',
                'quantity' => 1,
                'fulfillment_status' => 'pending',
                'notes' => 'For engagement shoot package.',
            ],
        ];

        foreach ($lineItems as $lineItem) {
            $variantId = $variantMap->get($lineItem['variant_name']);
            if (!$variantId) {
                continue;
            }

            $variant = InventoryVariant::find($variantId);

            ReservationItem::updateOrCreate(
                [
                    'reservation_id' => $lineItem['reservation_id'],
                    'variant_id' => $variantId,
                ],
                [
                    'item_id' => null,
                    'quantity' => $lineItem['quantity'],
                    'rental_price' => $variant?->rental_price ?? 0,
                    'fulfillment_status' => $lineItem['fulfillment_status'],
                    'notes' => $lineItem['notes'],
                ]
            );
        }
    }
}
