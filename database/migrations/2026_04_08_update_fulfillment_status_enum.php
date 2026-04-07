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
        Schema::table('reservation_items', function (Blueprint $table) {
            // Update ENUM to include 'partial' status for partial fulfillments
            // This allows tracking when some items from a reservation are released
            // but not all quantities have been fulfilled yet
            $table->enum('fulfillment_status', ['pending', 'partial', 'fulfilled', 'cancelled'])
                ->default('pending')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_items', function (Blueprint $table) {
            // Revert back to original ENUM
            // Note: If there are 'partial' records, this will fail
            // In production, you'd need to handle this case
            $table->enum('fulfillment_status', ['pending', 'fulfilled', 'cancelled'])
                ->default('pending')
                ->change();
        });
    }
};
