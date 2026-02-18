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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id('item_id');
            $table->enum('item_type', ['gown', 'suit']);
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('size');
            $table->string('color');
            $table->string('design');
            $table->decimal('rental_price', 10,2)->default(0);
            $table->decimal('deposit_amount', 10,2)->default(0);
            $table->boolean('is_sellable')->default(false);
            $table->decimal('selling_price', 10,2)->nullable()->comment('Sale price for occasional item sales');
            $table->foreignId('status_id')->constrained('inventory_statuses', 'status_id')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users', 'user_id')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['status_id', 'item_type'], 'inventories_status_item_type_idx');
            $table->index(['status_id', 'created_at'], 'inventories_status_created_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
