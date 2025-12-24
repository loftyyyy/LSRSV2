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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->cascadeOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations', 'reservation_id')->nullOnDelete();
            $table->foreignId('rental_id')->nullable()->constrained('rentals', 'rental_id')->nullOnDelete();

            // Financial summary
            $table->decimal('subtotal', 10, 2)->default(0); // Sum of all line items
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0); // Final amount due
            $table->decimal('amount_paid', 10, 2)->default(0); // Total payments received
            $table->decimal('balance_due', 10, 2)->default(0); // Remaining balance

            $table->dateTime('invoice_date');
            $table->dateTime('due_date')->nullable();
            $table->enum('invoice_type', ['reservation', 'rental', 'final']); // What type of invoice
            $table->foreignId('created_by')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('payment_statuses', 'status_id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
