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
        Schema::create('inventory_variants', function (Blueprint $table) {
            $table->id('variant_id');
            $table->string('variant_sku')->unique();
            $table->enum('item_type', ['gown', 'suit']);
            $table->string('name');
            $table->string('size');
            $table->string('color');
            $table->string('design');
            $table->decimal('rental_price', 10,2)->default(0);
            $table->decimal('deposit_amount', 10,2)->default(0);
            $table->boolean('is_sellable')->default(false);
            $table->decimal('selling_price', 10,2)->nullable();
            $table->unsignedInteger('total_units')->default(0);
            $table->unsignedInteger('available_units')->default(0);
            $table->timestamps();

            $table->index(['item_type', 'size', 'color', 'design'], 'inv_variants_type_size_color_design_idx');
            $table->index(['item_type', 'name'], 'inv_variants_type_name_idx');
        });

        Schema::create('inventories', function (Blueprint $table) {
            $table->id('item_id');
            $table->foreignId('variant_id')->nullable()->constrained('inventory_variants', 'variant_id')->nullOnDelete();
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
            $table->index(['variant_id', 'status_id'], 'inventories_variant_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('inventory_variants');
    }
};
