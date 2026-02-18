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
            
            // Deposit tracking fields
            $table->decimal('deposit_amount', 10, 2)->default(0)->comment('Security deposit amount collected at pickup');
            $table->enum('deposit_status', [
                'not_collected',      // No deposit required or not yet collected
                'held',               // Deposit currently held
                'returned_full',      // Full amount returned to customer
                'returned_partial',   // Partial return (deductions applied)
                'forfeited'           // Entire deposit forfeited (damage/no-show)
            ])->default('not_collected');
            $table->decimal('deposit_returned_amount', 10, 2)->default(0)->comment('Amount actually returned to customer');
            $table->decimal('deposit_deducted_amount', 10, 2)->default(0)->comment('Amount deducted from deposit (damage, late fees, etc.)');
            $table->foreignId('deposit_collected_by')->nullable()->constrained('users', 'user_id')->nullOnDelete()->comment('Staff who collected the deposit');
            $table->dateTime('deposit_collected_at')->nullable()->comment('When deposit was collected');
            
            $table->timestamps();
            
            // Indexes for deposit reporting
            $table->index(['deposit_status', 'deposit_collected_at']);
            $table->index(['customer_id', 'deposit_status']);

            // Indexes for performance
            $table->index(['return_date', 'due_date'], 'rentals_return_due_idx');
            $table->index(['status_id', 'created_at'], 'rentals_status_created_at_idx');
            $table->index(['customer_id', 'return_date'], 'rentals_customer_return_date_idx');
            $table->index(['item_id', 'created_at'], 'rentals_item_created_at_idx');
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
