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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('rental_id')->nullable()->constrained('rentals', 'rental_id')->cascadeOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations', 'reservation_id')->cascadeOnDelete();
            $table->enum('payment_type', ['rental_fee', 'deposit']);
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer']);
            $table->date('payment_date');
            $table->decimal('amount', 10,2);
            $table->foreignId('processed_by')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('payment_statuses', 'status_id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
