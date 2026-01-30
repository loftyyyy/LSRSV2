<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds indexes for performance optimization
     */
    public function up(): void
    {
        // Add index on rentals.due_date for overdue checks
        if (!$this->indexExists('rentals', 'due_date')) {
            Schema::table('rentals', function (Blueprint $table) {
                $table->index('due_date');
            });
        }

        // Add indexes on reservations for availability checks
        if (!$this->indexExists('reservations', 'start_date')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->index('start_date');
            });
        }

        if (!$this->indexExists('reservations', 'end_date')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->index('end_date');
            });
        }

        // Add index on invoices.status_id for filtering (if not using foreign key index)
        if (!$this->indexExists('invoices', 'status_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                // Status_id should already have an index from foreign key, so skip
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            if ($this->indexExists('rentals', 'due_date')) {
                $table->dropIndex(['due_date']);
            }
        });

        Schema::table('reservations', function (Blueprint $table) {
            if ($this->indexExists('reservations', 'start_date')) {
                $table->dropIndex(['start_date']);
            }
            if ($this->indexExists('reservations', 'end_date')) {
                $table->dropIndex(['end_date']);
            }
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $column): bool
    {
        try {
            $indexes = \DB::select("SELECT DISTINCT INDEX_NAME FROM information_schema.STATISTICS 
                                   WHERE TABLE_NAME = ? AND COLUMN_NAME = ?", [$table, $column]);
            return !empty($indexes);
        } catch (\Exception $e) {
            return false;
        }
    }
};
