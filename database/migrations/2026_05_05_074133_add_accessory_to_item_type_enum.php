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
        Schema::table('inventory_variants', function (Blueprint $table) {
            $table->enum('item_type', ['gown', 'suit', 'accessory'])->change();
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->enum('item_type', ['gown', 'suit', 'accessory'])->change();
        });

        // Make image_url nullable
        Schema::table('inventory_images', function (Blueprint $table) {
            $table->string('image_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_variants', function (Blueprint $table) {
            $table->enum('item_type', ['gown', 'suit'])->change();
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->enum('item_type', ['gown', 'suit'])->change();
        });

        Schema::table('inventory_images', function (Blueprint $table) {
            $table->string('image_url')->nullable(false)->change();
        });
    }
};
