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
            $table->enum('fulfillment_status', ['pending', 'fulfilled', 'cancelled'])->default('pending');
            $table->string('notes')->nullable();
            $table->integer('quantity')->default(1); // If you rent multiple of same item
            $table->decimal('rental_price', 10, 2)->nullable();
            $table->timestamps();
            $table->unique(['reservation_id', 'item_id']); // Prevent duplicate items in same reservation
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_items');
    }
};
