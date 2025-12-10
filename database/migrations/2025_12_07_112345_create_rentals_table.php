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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id('rental_id');
            $table->foreignId('reservation_id')->nullable()->constrained('reservations', 'reservation_id')->nullOnDelete();
            $table->foreignId('item_id')->constrained('inventories', 'item_id')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->cascadeOnDelete();
            $table->foreignId('released_by')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->date('released_date');
            $table->date('due_date');
            $table->date('return_date');
            $table->decimal('rental_price', 10,2);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->decimal('penalty_fee', 10,2)->default(0);
            $table->foreignId('status_id')->constrained('rental_statuses', 'status_id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
