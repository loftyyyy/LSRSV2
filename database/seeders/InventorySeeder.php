<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\InventoryStatus;
use App\Models\InventoryVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statusIds = InventoryStatus::pluck('status_id', 'status_name');
        $availableStatusId = $statusIds->get('available');
        $defaultUserId = DB::table('users')->value('user_id');

        if (!$availableStatusId) {
            return;
        }

        $catalog = [
            [
                'item_type' => 'gown',
                'name' => 'Aurora Lace Bridal Gown',
                'size' => 'M',
                'color' => 'Ivory',
                'design' => 'Mermaid Lace',
                'rental_price' => 4500,
                'deposit_amount' => 6000,
                'is_sellable' => false,
                'selling_price' => null,
                'stock' => ['available', 'available', 'rented', 'maintenance'],
            ],
            [
                'item_type' => 'gown',
                'name' => 'Selene Satin Ball Gown',
                'size' => 'L',
                'color' => 'Champagne',
                'design' => 'Ball Gown Satin',
                'rental_price' => 5200,
                'deposit_amount' => 7000,
                'is_sellable' => true,
                'selling_price' => 38000,
                'stock' => ['available', 'available', 'available', 'reserved'],
            ],
            [
                'item_type' => 'gown',
                'name' => 'Celeste Off-Shoulder Evening Gown',
                'size' => 'S',
                'color' => 'Navy Blue',
                'design' => 'Off-Shoulder Chiffon',
                'rental_price' => 2800,
                'deposit_amount' => 3500,
                'is_sellable' => true,
                'selling_price' => 16500,
                'stock' => ['available', 'available', 'rented'],
            ],
            [
                'item_type' => 'gown',
                'name' => 'Luna Debutante Tulle Gown',
                'size' => 'XS',
                'color' => 'Blush Pink',
                'design' => 'Layered Tulle',
                'rental_price' => 3600,
                'deposit_amount' => 5000,
                'is_sellable' => false,
                'selling_price' => null,
                'stock' => ['available', 'available', 'maintenance'],
            ],
            [
                'item_type' => 'suit',
                'name' => 'Sterling Classic Tuxedo',
                'size' => '40R',
                'color' => 'Black',
                'design' => 'Peak Lapel',
                'rental_price' => 2500,
                'deposit_amount' => 3000,
                'is_sellable' => true,
                'selling_price' => 14500,
                'stock' => ['available', 'available', 'available', 'rented', 'reserved'],
            ],
            [
                'item_type' => 'suit',
                'name' => 'Monarch Three-Piece Suit',
                'size' => '38R',
                'color' => 'Navy',
                'design' => 'Three-Piece Slim Fit',
                'rental_price' => 2700,
                'deposit_amount' => 3200,
                'is_sellable' => false,
                'selling_price' => null,
                'stock' => ['available', 'available', 'rented'],
            ],
            [
                'item_type' => 'suit',
                'name' => 'Regal Wedding Barong Set',
                'size' => 'L',
                'color' => 'Cream',
                'design' => 'Barong with Trousers',
                'rental_price' => 2200,
                'deposit_amount' => 2800,
                'is_sellable' => true,
                'selling_price' => 12000,
                'stock' => ['available', 'available', 'available', 'maintenance'],
            ],
            [
                'item_type' => 'suit',
                'name' => 'Crown Prince Kids Suit',
                'size' => '10',
                'color' => 'Gray',
                'design' => 'Kids Formal Set',
                'rental_price' => 1200,
                'deposit_amount' => 1500,
                'is_sellable' => true,
                'selling_price' => 6500,
                'stock' => ['available', 'reserved'],
            ],
        ];

        DB::transaction(function () use ($catalog, $statusIds, $availableStatusId, $defaultUserId) {
            foreach ($catalog as $index => $variantData) {
                $stockStates = $variantData['stock'];
                unset($variantData['stock']);

                $variant = InventoryVariant::create(array_merge($variantData, [
                    'total_units' => count($stockStates),
                    'available_units' => collect($stockStates)->filter(fn ($state) => $state === 'available')->count(),
                ]));

                foreach ($stockStates as $unitIndex => $statusName) {
                    $statusId = $statusIds->get($statusName, $availableStatusId);
                    $prefix = strtoupper(substr($variant->item_type, 0, 3));
                    $variantSegment = str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
                    $unitSegment = str_pad((string) ($unitIndex + 1), 2, '0', STR_PAD_LEFT);

                    Inventory::create([
                        'variant_id' => $variant->variant_id,
                        'item_type' => $variant->item_type,
                        'sku' => "{$prefix}-{$variantSegment}-{$unitSegment}",
                        'name' => $variant->name,
                        'size' => $variant->size,
                        'color' => $variant->color,
                        'design' => $variant->design,
                        'rental_price' => $variant->rental_price,
                        'deposit_amount' => $variant->deposit_amount,
                        'is_sellable' => $variant->is_sellable,
                        'selling_price' => $variant->selling_price,
                        'status_id' => $statusId,
                        'updated_by' => $defaultUserId,
                    ]);
                }
            }
        });
    }
}
