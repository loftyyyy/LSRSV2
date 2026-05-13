<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn(['rental_price', 'deposit_amount', 'is_sellable', 'selling_price']);
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->decimal('rental_price', 10, 2)->default(0);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->boolean('is_sellable')->default(false);
            $table->decimal('selling_price', 10, 2)->nullable()->comment('Sale price for occasional item sales');
        });
    }
};