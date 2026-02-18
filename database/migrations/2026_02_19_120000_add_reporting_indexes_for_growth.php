<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->addIndexIfMissing('customers', ['status_id', 'created_at'], 'customers_status_created_at_idx');

        $this->addIndexIfMissing('inventories', ['status_id', 'item_type'], 'inventories_status_item_type_idx');
        $this->addIndexIfMissing('inventories', ['status_id', 'created_at'], 'inventories_status_created_at_idx');

        $this->addIndexIfMissing('reservations', ['status_id', 'reservation_date'], 'reservations_status_reservation_date_idx');
        $this->addIndexIfMissing('reservations', ['customer_id', 'reservation_date'], 'reservations_customer_reservation_date_idx');
        $this->addIndexIfMissing('reservations', ['reserved_by', 'reservation_date'], 'reservations_reserved_by_reservation_date_idx');
        $this->addIndexIfMissing('reservations', ['status_id', 'start_date', 'end_date'], 'reservations_status_start_end_idx');

        $this->addIndexIfMissing('rentals', ['return_date', 'due_date'], 'rentals_return_due_idx');
        $this->addIndexIfMissing('rentals', ['status_id', 'created_at'], 'rentals_status_created_at_idx');
        $this->addIndexIfMissing('rentals', ['customer_id', 'return_date'], 'rentals_customer_return_date_idx');
        $this->addIndexIfMissing('rentals', ['item_id', 'created_at'], 'rentals_item_created_at_idx');

        $this->addIndexIfMissing('invoices', ['status_id', 'invoice_date'], 'invoices_status_invoice_date_idx');
        $this->addIndexIfMissing('invoices', ['customer_id', 'invoice_date'], 'invoices_customer_invoice_date_idx');
        $this->addIndexIfMissing('invoices', ['balance_due', 'due_date'], 'invoices_balance_due_date_idx');
        $this->addIndexIfMissing('invoices', ['created_by', 'created_at'], 'invoices_created_by_created_at_idx');

        $this->addIndexIfMissing('payments', ['status_id', 'payment_date'], 'payments_status_payment_date_idx');
        $this->addIndexIfMissing('payments', ['invoice_id', 'payment_date'], 'payments_invoice_payment_date_idx');
        $this->addIndexIfMissing('payments', ['processed_by', 'created_at'], 'payments_processed_by_created_at_idx');

        $this->addIndexIfMissing('reservation_items', ['item_id', 'fulfillment_status'], 'reservation_items_item_fulfillment_idx');
        $this->addIndexIfMissing('reservation_items', ['reservation_id', 'fulfillment_status'], 'reservation_items_reservation_fulfillment_idx');

        $this->addIndexIfMissing('invoice_items', ['invoice_id', 'item_type'], 'invoice_items_invoice_item_type_idx');
        $this->addIndexIfMissing('invoice_items', ['item_id', 'item_type'], 'invoice_items_item_item_type_idx');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexIfExists('customers', 'customers_status_created_at_idx');

        $this->dropIndexIfExists('inventories', 'inventories_status_item_type_idx');
        $this->dropIndexIfExists('inventories', 'inventories_status_created_at_idx');

        $this->dropIndexIfExists('reservations', 'reservations_status_reservation_date_idx');
        $this->dropIndexIfExists('reservations', 'reservations_customer_reservation_date_idx');
        $this->dropIndexIfExists('reservations', 'reservations_reserved_by_reservation_date_idx');
        $this->dropIndexIfExists('reservations', 'reservations_status_start_end_idx');

        $this->dropIndexIfExists('rentals', 'rentals_return_due_idx');
        $this->dropIndexIfExists('rentals', 'rentals_status_created_at_idx');
        $this->dropIndexIfExists('rentals', 'rentals_customer_return_date_idx');
        $this->dropIndexIfExists('rentals', 'rentals_item_created_at_idx');

        $this->dropIndexIfExists('invoices', 'invoices_status_invoice_date_idx');
        $this->dropIndexIfExists('invoices', 'invoices_customer_invoice_date_idx');
        $this->dropIndexIfExists('invoices', 'invoices_balance_due_date_idx');
        $this->dropIndexIfExists('invoices', 'invoices_created_by_created_at_idx');

        $this->dropIndexIfExists('payments', 'payments_status_payment_date_idx');
        $this->dropIndexIfExists('payments', 'payments_invoice_payment_date_idx');
        $this->dropIndexIfExists('payments', 'payments_processed_by_created_at_idx');

        $this->dropIndexIfExists('reservation_items', 'reservation_items_item_fulfillment_idx');
        $this->dropIndexIfExists('reservation_items', 'reservation_items_reservation_fulfillment_idx');

        $this->dropIndexIfExists('invoice_items', 'invoice_items_invoice_item_type_idx');
        $this->dropIndexIfExists('invoice_items', 'invoice_items_item_item_type_idx');
    }

    private function addIndexIfMissing(string $tableName, array $columns, string $indexName): void
    {
        if ($this->indexExists($tableName, $indexName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
            $table->index($columns, $indexName);
        });
    }

    private function dropIndexIfExists(string $tableName, string $indexName): void
    {
        if (! $this->indexExists($tableName, $indexName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($indexName) {
            $table->dropIndex($indexName);
        });
    }

    private function indexExists(string $tableName, string $indexName): bool
    {
        $connection = Schema::getConnection();

        if ($connection->getDriverName() === 'sqlite') {
            return false;
        }

        $databaseName = $connection->getDatabaseName();

        if (! is_string($databaseName) || $databaseName === '') {
            return false;
        }

        $result = DB::selectOne(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$databaseName, $tableName, $indexName]
        );

        return $result !== null;
    }
};
