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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id('movement_id');
            $table->foreignId('item_id')
                ->constrained('inventories', 'item_id')
                ->cascadeOnDelete();
            $table->foreignId('variant_id')
                ->nullable()
                ->constrained('inventory_variants', 'variant_id')
                ->nullOnDelete();
            $table->foreignId('reservation_id')
                ->nullable()
                ->constrained('reservations', 'reservation_id')
                ->nullOnDelete();
            $table->foreignId('reservation_item_id')
                ->nullable()
                ->constrained('reservation_items', 'reservation_item_id')
                ->nullOnDelete();
            $table->foreignId('rental_id')
                ->nullable()
                ->constrained('rentals', 'rental_id')
                ->nullOnDelete();
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
            $table->foreignId('from_status_id')
                ->nullable()
                ->constrained('inventory_statuses', 'status_id')
                ->nullOnDelete();
            $table->foreignId('to_status_id')
                ->nullable()
                ->constrained('inventory_statuses', 'status_id')
                ->nullOnDelete();
            $table->foreignId('performed_by')
                ->nullable()
                ->constrained('users', 'user_id')
                ->nullOnDelete();
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
    }
};
