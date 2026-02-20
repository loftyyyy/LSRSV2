<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            $this->backfillInventoryVariants();
            $this->backfillReservationItemVariants();
            $this->refreshVariantCounters();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            DB::table('reservation_items')->update(['variant_id' => null]);
            DB::table('inventories')->update(['variant_id' => null]);
            DB::table('inventory_variants')->delete();
        });
    }

    private function backfillInventoryVariants(): void
    {
        $now = now();
        $variantMap = [];

        DB::table('inventories')
            ->select([
                'item_id',
                'item_type',
                'name',
                'size',
                'color',
                'design',
                'rental_price',
                'deposit_amount',
                'is_sellable',
                'selling_price',
            ])
            ->orderBy('item_id')
            ->chunk(200, function ($items) use (&$variantMap, $now) {
                foreach ($items as $item) {
                    $key = implode('|', [
                        $item->item_type,
                        $item->name,
                        $item->size,
                        $item->color,
                        $item->design,
                        (string) $item->rental_price,
                        (string) $item->deposit_amount,
                        (string) $item->is_sellable,
                        $item->selling_price === null ? 'null' : (string) $item->selling_price,
                    ]);

                    if (!isset($variantMap[$key])) {
                        $variantId = DB::table('inventory_variants')
                            ->where('item_type', $item->item_type)
                            ->where('name', $item->name)
                            ->where('size', $item->size)
                            ->where('color', $item->color)
                            ->where('design', $item->design)
                            ->where('rental_price', $item->rental_price)
                            ->where('deposit_amount', $item->deposit_amount)
                            ->where('is_sellable', $item->is_sellable)
                            ->where(function ($query) use ($item) {
                                if ($item->selling_price === null) {
                                    $query->whereNull('selling_price');
                                } else {
                                    $query->where('selling_price', $item->selling_price);
                                }
                            })
                            ->value('variant_id');

                        if (!$variantId) {
                            $variantId = DB::table('inventory_variants')->insertGetId([
                                'item_type' => $item->item_type,
                                'name' => $item->name,
                                'size' => $item->size,
                                'color' => $item->color,
                                'design' => $item->design,
                                'rental_price' => $item->rental_price,
                                'deposit_amount' => $item->deposit_amount,
                                'is_sellable' => $item->is_sellable,
                                'selling_price' => $item->selling_price,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }

                        $variantMap[$key] = $variantId;
                    }

                    DB::table('inventories')
                        ->where('item_id', $item->item_id)
                        ->update(['variant_id' => $variantMap[$key]]);
                }
            });
    }

    private function backfillReservationItemVariants(): void
    {
        DB::table('reservation_items')
            ->select(['reservation_item_id', 'item_id'])
            ->orderBy('reservation_item_id')
            ->chunk(200, function ($rows) {
                foreach ($rows as $row) {
                    $variantId = DB::table('inventories')
                        ->where('item_id', $row->item_id)
                        ->value('variant_id');

                    if ($variantId) {
                        DB::table('reservation_items')
                            ->where('reservation_item_id', $row->reservation_item_id)
                            ->update(['variant_id' => $variantId]);
                    }
                }
            });
    }

    private function refreshVariantCounters(): void
    {
        $availableStatusId = DB::table('inventory_statuses')
            ->where('status_name', 'available')
            ->value('status_id');

        $variantIds = DB::table('inventory_variants')->pluck('variant_id');

        foreach ($variantIds as $variantId) {
            $totalUnits = DB::table('inventories')
                ->where('variant_id', $variantId)
                ->count();

            $availableUnits = $availableStatusId
                ? DB::table('inventories')
                    ->where('variant_id', $variantId)
                    ->where('status_id', $availableStatusId)
                    ->count()
                : 0;

            DB::table('inventory_variants')
                ->where('variant_id', $variantId)
                ->update([
                    'total_units' => $totalUnits,
                    'available_units' => $availableUnits,
                    'updated_at' => now(),
                ]);
        }
    }
};
