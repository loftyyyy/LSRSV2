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
            $table->enum('condition', ['good', 'damaged', 'under repair', 'retired'])->default('good');
            $table->decimal('rental_price', 10,2)->default(0);
            $table->foreignId('status_id')->constrained('inventory_statuses', 'status_id')->cascadeOnDelete();
            $table->timestamps();
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
