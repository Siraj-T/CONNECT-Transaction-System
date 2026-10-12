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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'recipient_citizen_id')) {
                $table->foreignId('recipient_citizen_id')->nullable()->after('citizen_id')->constrained('citizens')->nullOnDelete();
            }
            if (!Schema::hasColumn('transactions', 'type')) {
                $table->string('type', 30)->default('transfer')->after('payment_method_id');
            }
            if (!Schema::hasColumn('transactions', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'recipient_citizen_id')) {
                $table->dropForeign(['recipient_citizen_id']);
                $table->dropColumn('recipient_citizen_id');
            }
            if (Schema::hasColumn('transactions', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('transactions', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};
