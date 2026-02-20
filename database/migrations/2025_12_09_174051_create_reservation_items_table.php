<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservation_items', function (Blueprint $table) {
            $table->id('reservation_item_id');
            $table->foreignId('reservation_id')->constrained('reservations', 'reservation_id')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('inventories', 'item_id')->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('inventory_variants', 'variant_id')->nullOnDelete();
            $table->enum('fulfillment_status', ['pending', 'fulfilled', 'cancelled'])->default('pending');
            $table->string('notes')->nullable();
            $table->integer('quantity')->default(1); // If you rent multiple of same item
            $table->decimal('rental_price', 10, 2)->nullable();
            $table->timestamps();
            $table->unique(['reservation_id', 'item_id']); // Prevent duplicate items in same reservation

            $table->index(['item_id', 'fulfillment_status'], 'reservation_items_item_fulfillment_idx');
            $table->index(['reservation_id', 'fulfillment_status'], 'reservation_items_reservation_fulfillment_idx');
            $table->index(['variant_id', 'fulfillment_status'], 'reservation_items_variant_fulfillment_idx');
        });

        Schema::create('reservation_item_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_item_id')->constrained('reservation_items', 'reservation_item_id')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('inventories', 'item_id')->cascadeOnDelete();
            $table->enum('allocation_status', ['allocated', 'released', 'returned', 'cancelled'])->default('allocated');
            $table->timestamp('allocated_at')->useCurrent();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->timestamps();

            $table->unique(['reservation_item_id', 'item_id'], 'reservation_item_alloc_unique');
            $table->index(['item_id', 'allocation_status'], 'reservation_item_alloc_item_status_idx');
            $table->index(['reservation_item_id', 'allocation_status'], 'reservation_item_alloc_res_status_idx');
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id('movement_id');
            $table->foreignId('item_id')->constrained('inventories', 'item_id')->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('inventory_variants', 'variant_id')->nullOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations', 'reservation_id')->nullOnDelete();
            $table->foreignId('reservation_item_id')->nullable()->constrained('reservation_items', 'reservation_item_id')->nullOnDelete();
            $table->foreignId('rental_id')->nullable()->constrained('rentals', 'rental_id')->nullOnDelete();
            $table->enum('movement_type', [
                'reserve',
                'unreserve',
                'release',
                'return',
                'damage',
                'repair_out',
                'repair_in',
                'retire',
                'adjustment',
            ]);
            $table->unsignedInteger('quantity')->default(1);
            $table->foreignId('from_status_id')->nullable()->constrained('inventory_statuses', 'status_id')->nullOnDelete();
            $table->foreignId('to_status_id')->nullable()->constrained('inventory_statuses', 'status_id')->nullOnDelete();
            $table->foreignId('performed_by')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'created_at'], 'inventory_movements_item_created_idx');
            $table->index(['variant_id', 'created_at'], 'inventory_movements_variant_created_idx');
            $table->index(['movement_type', 'created_at'], 'inventory_movements_type_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('reservation_item_allocations');
        Schema::dropIfExists('reservation_items');
    }
};
