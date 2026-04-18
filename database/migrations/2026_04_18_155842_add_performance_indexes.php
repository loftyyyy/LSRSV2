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
        Schema::table('payments', function (Blueprint $table) {
            $table->index('payment_reference');
            $table->index('payment_method');
        });

        Schema::table('rentals', function (Blueprint $table) {
            $table->index('reservation_id');
            $table->index('released_date');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->index('reservation_date');
        });

        Schema::table('reservation_items', function (Blueprint $table) {
            $table->index('reservation_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index('invoice_type');
            $table->index('rental_id');
            $table->index('reservation_id');
        });
        
        Schema::table('inventories', function (Blueprint $table) {
            $table->index('item_type');
            $table->index(['name', 'size', 'color']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payment_reference']);
            $table->dropIndex(['payment_method']);
        });

        Schema::table('rentals', function (Blueprint $table) {
            $table->dropIndex(['reservation_id']);
            $table->dropIndex(['released_date']);
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['reservation_date']);
        });

        Schema::table('reservation_items', function (Blueprint $table) {
            $table->dropIndex(['reservation_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['invoice_type']);
            $table->dropIndex(['rental_id']);
            $table->dropIndex(['reservation_id']);
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex(['item_type']);
            $table->dropIndex(['name', 'size', 'color']);
        });
    }
};
