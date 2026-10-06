<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('e.g. 10GB - 7 Days Standard');
            $table->text('description')->nullable();
            $table->decimal('data_limit_gb', 8, 2)->nullable()->comment('NULL = unlimited');
            $table->unsignedInteger('duration_days')->comment('Validity period after redemption');
            $table->decimal('price_lyd', 10, 3)->comment('Cost price to admin');
            $table->decimal('reseller_price_lyd', 10, 3)->comment('Price reseller pays');
            $table->decimal('retail_price_lyd', 10, 3)->comment('Suggested retail price');
            $table->unsignedInteger('speed_mbps')->nullable()->comment('Download speed cap, NULL = unlimited');
            $table->boolean('is_active')->default(true);
            $table->char('color_hex', 7)->default('#0071E3')->comment('Card accent color');
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_plans');
    }
};
