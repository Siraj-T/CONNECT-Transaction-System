<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no', 30)->unique()->comment('Format: TXN-YYYYMMDD-XXXXX');
            $table->enum('type', ['purchase', 'sale', 'refund', 'wallet_topup', 'commission'])
                  ->comment('Transaction type');
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])
                  ->default('pending');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict')
                  ->comment('Who initiated the transaction');
            $table->foreignId('counterpart_id')->nullable()->constrained('users')->onDelete('set null')
                  ->comment('Other party involved (e.g. reseller for admin, customer for reseller)');
            $table->decimal('total_amount', 12, 3);
            $table->char('currency', 3)->default('LYD');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable()->comment('Flexible extra data (batch_id, plan_id, etc.)');
            $table->timestamps();

            $table->index('user_id');
            $table->index(['type', 'status']);
            $table->index('reference_no');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
