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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('invoice_id')->constrained('invoices', 'invoice_id')->cascadeOnDelete();
            $table->string('payment_reference')->nullable(); // Transaction/receipt number
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'gcash', 'paymaya']);
            $table->dateTime('payment_date');
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('payment_statuses', 'status_id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
