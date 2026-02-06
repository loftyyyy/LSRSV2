<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates deposit_returns table for tracking deposit returns and deductions
     */
    public function up(): void
    {
        Schema::create('deposit_returns', function (Blueprint $table) {
            $table->id('return_id');
            
            // Link to rental
            $table->foreignId('rental_id')->constrained('rentals', 'rental_id')->cascadeOnDelete()
                ->comment('Rental this deposit return belongs to');
            
            // Customer and financial tracking
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')
                ->comment('Customer who receives the return');
            
            $table->decimal('original_deposit_amount', 10, 2)
                ->comment('Original deposit amount held');
            
            $table->decimal('amount_returned', 10, 2)
                ->comment('Amount actually returned to customer');
            
            $table->decimal('amount_deducted', 10, 2)->default(0)
                ->comment('Amount deducted from deposit');
            
            // Deduction breakdown (optional detailed tracking)
            $table->json('deductions_breakdown')->nullable()
                ->comment('JSON array of deductions: [{"type": "damage", "amount": 500, "reason": "..."}, ...]');
            
            // Return method and reference
            $table->enum('return_method', ['cash', 'bank_transfer', 'gcash', 'paymaya', 'check'])
                ->comment('How deposit was returned');
            
            $table->string('return_reference')->nullable()
                ->comment('Transaction reference number for the return');
            
            // Link to inspection (if deductions were made)
            $table->foreignId('inspection_id')->nullable()
                ->comment('Reference to inspection report that justified deductions');
            
            // Status and notes
            $table->enum('status', ['pending', 'processed', 'cancelled'])->default('pending')
                ->comment('Status of the return transaction');
            
            $table->text('notes')->nullable()
                ->comment('Additional notes about the return');
            
            // Audit fields
            $table->foreignId('processed_by')->constrained('users', 'user_id')
                ->comment('Staff who processed the return');
            
            $table->dateTime('processed_at')->nullable()
                ->comment('When the return was processed');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['rental_id', 'status']);
            $table->index(['customer_id', 'processed_at']);
            $table->index(['status', 'processed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_returns');
    }
};
