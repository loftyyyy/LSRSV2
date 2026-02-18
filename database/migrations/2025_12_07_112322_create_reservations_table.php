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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('reservation_id');
            $table->foreignId('reserved_by')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('reservation_statuses', 'status_id')->cascadeOnDelete();
            $table->date('reservation_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->index(['status_id', 'reservation_date'], 'reservations_status_reservation_date_idx');
            $table->index(['customer_id', 'reservation_date'], 'reservations_customer_reservation_date_idx');
            $table->index(['reserved_by', 'reservation_date'], 'reservations_reserved_by_reservation_date_idx');
            $table->index(['status_id', 'start_date', 'end_date'], 'reservations_status_start_end_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
