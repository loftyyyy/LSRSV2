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
        Schema::table('reservations', function (Blueprint $table) {
            // Add confirmation tracking columns
            $table->timestamp('confirmed_at')->nullable()->after('end_date');
            $table->foreignId('confirmed_by')->nullable()->after('confirmed_at')
                ->constrained('users', 'user_id')
                ->nullOnDelete();

            // Add cancellation tracking columns
            $table->timestamp('cancelled_at')->nullable()->after('confirmed_by');
            $table->foreignId('cancelled_by')->nullable()->after('cancelled_at')
                ->constrained('users', 'user_id')
                ->nullOnDelete();
            $table->text('cancellation_reason')->nullable()->after('cancelled_by');

            // Add expiry support for future feature
            $table->date('expiry_date')->nullable()->after('cancellation_reason');
            $table->timestamp('expiry_checked_at')->nullable()->after('expiry_date');

            // Add soft deletes for audit compliance
            $table->softDeletes();

            // Add indexes for frequently queried status combinations
            $table->index(['status_id', 'confirmed_at'], 'reservations_status_confirmed_idx');
            $table->index(['status_id', 'cancelled_at'], 'reservations_status_cancelled_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('reservations_status_confirmed_idx');
            $table->dropIndex('reservations_status_cancelled_idx');

            // Drop columns
            $table->dropColumn([
                'confirmed_at',
                'confirmed_by',
                'cancelled_at',
                'cancelled_by',
                'cancellation_reason',
                'expiry_date',
                'expiry_checked_at',
                'deleted_at',
            ]);

            // Drop foreign keys if they still exist
            $table->dropForeignKeyIfExists(['confirmed_by', 'cancelled_by']);
        });
    }
};
