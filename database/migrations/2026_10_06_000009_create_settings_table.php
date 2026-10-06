<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique()->comment('e.g. company.name, voucher.code_prefix');
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string')->comment('string, boolean, json, integer');
            $table->string('group', 50)->default('general')->comment('general, voucher, notification, billing');
            $table->string('label', 100)->comment('Human-readable label for the settings UI');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
