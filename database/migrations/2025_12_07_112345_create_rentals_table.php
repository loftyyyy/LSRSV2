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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id('rental_id');
            $table->foreignId('reservation_id')->nullable()->constrained('reservations', 'reservation_id')->nullOnDelete();
            $table->foreignId('item_id')->constrained('inventories', 'item_id')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->cascadeOnDelete();
            $table->foreignId('released_by')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->date('released_date');
            $table->date('due_date');
            $table->date('original_due_date'); // Original due date (set when rental is created, unchanged by extensions)
            $table->integer('extension_count')->default(0); // Number of times rental was officially extended
            $table->foreignId('extended_by')->nullable()->constrained('users', 'user_id')->nullOnDelete(); // User who authorized the last extension
            $table->dateTime('last_extended_at')->nullable(); // When the rental was last extended
            $table->text('extension_reason')->nullable(); // Reason/notes for the extension
            $table->date('return_date')->nullable();
            $table->foreignId('returned_to')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->text('return_notes')->nullable(); // Document damages, condition, etc.
            $table->foreignId('status_id')->constrained('rental_statuses', 'status_id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
