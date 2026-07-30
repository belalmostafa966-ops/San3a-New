<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('set null');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('set null');
        });

        Schema::table('visit_fee_holds', function (Blueprint $table) {
            $table->foreign('job_request_id')->references('id')->on('job_requests')->onDelete('cascade');
        });

        Schema::table('cashback_ledger', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('set null');
        });

        Schema::table('payment_installments', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
        });

        Schema::table('visit_fee_holds', function (Blueprint $table) {
            $table->dropForeign(['job_request_id']);
        });

        Schema::table('cashback_ledger', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
        });

        Schema::table('payment_installments', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
        });
    }
};