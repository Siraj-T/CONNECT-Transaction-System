<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_plan_id')->constrained()->onDelete('restrict');
            $table->string('code', 50)->unique()->comment('Format: CONN-XXXX-XXXX-XXXX');
            $table->char('batch_id', 36)->comment('UUID grouping a generated batch');
            $table->enum('status', ['available', 'reserved', 'sold', 'redeemed', 'expired', 'cancelled'])
                  ->default('available');
            $table->foreignId('generated_by')->constrained('users')->onDelete('restrict')
                  ->comment('Admin who generated this batch');
            $table->foreignId('sold_by')->nullable()->constrained('users')->onDelete('set null')
                  ->comment('Reseller who sold this voucher');
            $table->foreignId('sold_to')->nullable()->constrained('users')->onDelete('set null')
                  ->comment('Customer who purchased');
            $table->foreignId('redeemed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('sell_price_lyd', 10, 3)->nullable()->comment('Actual price sold at');
            $table->timestamp('sold_at')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamp('expires_at')->nullable()->comment('Set on redemption based on plan duration');
            $table->timestamps();

            // Indexes for common queries
            $table->index('voucher_plan_id');
            $table->index('status');
            $table->index('batch_id');
            $table->index('sold_by');
            $table->index('redeemed_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
