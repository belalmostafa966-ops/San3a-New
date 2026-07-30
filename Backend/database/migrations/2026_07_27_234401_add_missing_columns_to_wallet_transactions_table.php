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
    Schema::table('wallet_transactions', function (Blueprint $table) {
        if (!Schema::hasColumn('wallet_transactions', 'balance_after')) {
            $table->decimal('balance_after', 10, 2)->nullable()->after('amount');
        }
        if (!Schema::hasColumn('wallet_transactions', 'job_id')) {
            $table->unsignedBigInteger('job_id')->nullable()->after('balance_after');
        }
    });
}

public function down(): void
{
    Schema::table('wallet_transactions', function (Blueprint $table) {
        $table->dropColumn(['balance_after', 'job_id']);
    });
}
};
