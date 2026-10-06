<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')
                  ->comment('The actor who performed the action');
            $table->string('action', 100)->comment('e.g. voucher.generated, user.login, wallet.topup');
            $table->string('subject_type', 100)->nullable()->comment('Model class name');
            $table->unsignedBigInteger('subject_id')->nullable()->comment('Model primary key');
            $table->json('old_values')->nullable()->comment('State before the change');
            $table->json('new_values')->nullable()->comment('State after the change');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('action');
            $table->index(['subject_type', 'subject_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
