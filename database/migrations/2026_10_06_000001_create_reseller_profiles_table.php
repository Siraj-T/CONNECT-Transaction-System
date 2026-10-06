<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseller_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->string('business_name', 150)->nullable();
            $table->decimal('wallet_balance', 12, 3)->default(0.000);
            $table->decimal('commission_rate', 5, 2)->default(5.00)->comment('Percentage commission per sale');
            $table->decimal('total_earned_commission', 12, 3)->default(0.000);
            $table->boolean('is_approved')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_profiles');
    }
};
