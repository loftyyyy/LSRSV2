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
        Schema::create('inventory_images', function (Blueprint $table) {
            $table->id('image_id'); // Changed from inventory_image_id
            $table->foreignId('item_id') // Changed from inventory_id
            ->constrained('inventories', 'item_id') // Specify the column name
            ->cascadeOnDelete();
            $table->string('image_path');
            $table->string('image_url'); // Added: Public URL for the image
            $table->enum('view_type', ['front', 'back', 'side', 'detail', 'full'])->nullable(); // Added
            $table->string('caption')->nullable(); // Added: Optional description
            $table->boolean('is_primary')->default(false);
            $table->integer('display_order')->default(0); // Added: For ordering images
            $table->unsignedBigInteger('file_size')->nullable(); // Added: File size in bytes
            $table->string('mime_type')->nullable(); // Added: e.g., image/jpeg
            $table->timestamps();

            // Indexes for better query performance
            $table->index('item_id');
            $table->index(['item_id', 'is_primary']);
            $table->index(['item_id', 'view_type']);
            $table->index(['item_id', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_images');
    }
};
