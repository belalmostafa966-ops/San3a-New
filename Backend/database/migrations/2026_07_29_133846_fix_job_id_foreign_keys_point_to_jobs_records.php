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
        $table->dropForeign(['job_id']);
        $table->foreign('job_id')->references('id')->on('jobs_records')->onDelete('set null');
    });
    Schema::table('payments', function (Blueprint $table) {
        $table->dropForeign(['job_id']);
        $table->foreign('job_id')->references('id')->on('jobs_records')->onDelete('set null');
    });
    Schema::table('cashback_ledger', function (Blueprint $table) {
        $table->dropForeign(['job_id']);
        $table->foreign('job_id')->references('id')->on('jobs_records')->onDelete('set null');
    });
    Schema::table('payment_installments', function (Blueprint $table) {
        $table->dropForeign(['job_id']);
        $table->foreign('job_id')->references('id')->on('jobs_records')->onDelete('set null');
    });
}

public function down(): void
{
    // رجوع للحالة القديمة لو احتجت
}
};
