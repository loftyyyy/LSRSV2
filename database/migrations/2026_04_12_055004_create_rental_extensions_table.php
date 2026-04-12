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
        Schema::create('rental_extensions', function (Blueprint $table) {
            $table->id('extension_id');
            $table->foreignId('rental_id')->constrained('rentals', 'rental_id')->cascadeOnDelete();
            $table->date('old_due_date');
            $table->date('new_due_date');
            $table->text('extension_reason')->nullable();
            $table->foreignId('extended_by')->constrained('users', 'user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_extensions');
    }
};
