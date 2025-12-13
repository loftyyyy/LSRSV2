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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices', 'invoice_id')->cascadeOnDelete();
            $table->string('description'); // "Gown Rental - Red Evening Gown"
            $table->enum('item_type', ['rental_fee', 'deposit', 'penalty', 'damage_fee', 'late_fee', 'cleaning_fee', 'other']);
            $table->foreignId('item_id')->nullable()->constrained('inventories', 'item_id')->nullOnDelete(); // Link to actual item if applicable
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2); // quantity * unit_price
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
