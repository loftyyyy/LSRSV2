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
        Schema::create('reservation_item_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_item_id')
                ->constrained('reservation_items', 'reservation_item_id')
                ->cascadeOnDelete();
            $table->foreignId('item_id')
                ->constrained('inventories', 'item_id')
                ->cascadeOnDelete();
            $table->enum('allocation_status', ['allocated', 'released', 'returned', 'cancelled'])
                ->default('allocated');
            $table->timestamp('allocated_at')->useCurrent();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users', 'user_id')
                ->nullOnDelete();
            $table->timestamps();

            $table->unique(['reservation_item_id', 'item_id'], 'reservation_item_alloc_unique');
            $table->index(['item_id', 'allocation_status'], 'reservation_item_alloc_item_status_idx');
            $table->index(['reservation_item_id', 'allocation_status'], 'reservation_item_alloc_res_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_item_allocations');
    }
};
