<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict')
                  ->comment('Reseller or customer wallet owner');
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 12, 3);
            $table->decimal('balance_after', 12, 3)->comment('Balance snapshot after this entry');
            $table->string('description', 255);
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('transaction_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_ledger');
    }
};
