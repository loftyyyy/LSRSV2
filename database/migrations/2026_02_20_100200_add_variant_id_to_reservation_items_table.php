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
            $table->foreignId('variant_id')
                ->nullable()
                ->after('item_id')
                ->constrained('inventory_variants', 'variant_id')
                ->nullOnDelete();

            $table->index(['variant_id', 'fulfillment_status'], 'reservation_items_variant_fulfillment_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_items', function (Blueprint $table) {
            $table->dropIndex('reservation_items_variant_fulfillment_idx');
            $table->dropConstrainedForeignId('variant_id');
        });
    }
};
