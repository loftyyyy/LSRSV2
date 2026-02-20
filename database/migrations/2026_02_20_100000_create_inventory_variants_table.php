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
            $table->enum('item_type', ['gown', 'suit']);
            $table->string('name');
            $table->string('size');
            $table->string('color');
            $table->string('design');
            $table->decimal('rental_price', 10, 2)->default(0);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->boolean('is_sellable')->default(false);
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->unsignedInteger('total_units')->default(0);
            $table->unsignedInteger('available_units')->default(0);
            $table->timestamps();

            $table->index(['item_type', 'size', 'color', 'design'], 'inv_variants_type_size_color_design_idx');
            $table->index(['item_type', 'name'], 'inv_variants_type_name_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_variants');
    }
};
