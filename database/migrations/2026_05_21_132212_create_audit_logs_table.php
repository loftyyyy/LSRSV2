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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id');
            $table->string('action'); // 'create', 'update', 'delete', 'login', 'logout', 'login_failed'
            $table->string('model_type')->nullable(); // Full model class name (e.g., App\Models\Customer)
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the model being audited
            $table->json('changes')->nullable(); // JSON of what changed: ['field' => ['old' => value, 'new' => value]]
            $table->string('ip_address', 45)->nullable(); // IPv4 or IPv6
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Indexes for common queries
            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index(['action']);
            $table->index(['ip_address']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
