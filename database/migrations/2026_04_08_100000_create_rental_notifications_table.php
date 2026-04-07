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
        Schema::create('rental_notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->unsignedBigInteger('rental_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('type', 50); // due_reminder, overdue_alert, return_confirmation, extension_reminder
            $table->string('title');
            $table->text('message');
            $table->string('priority', 20)->default('normal'); // low, normal, high, urgent
            $table->boolean('is_read')->default(false);
            $table->boolean('is_dismissed')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('dismissed_at')->nullable();
            $table->json('metadata')->nullable(); // Additional data like days_overdue, penalty_amount, etc.
            $table->timestamps();

            // Indexes
            $table->index(['is_read', 'is_dismissed']);
            $table->index(['type', 'created_at']);
            $table->index('rental_id');
            $table->index('customer_id');

            // Foreign keys
            $table->foreign('rental_id')
                ->references('rental_id')
                ->on('rentals')
                ->onDelete('cascade');

            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_notifications');
    }
};
