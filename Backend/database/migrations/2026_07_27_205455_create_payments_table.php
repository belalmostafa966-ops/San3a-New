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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('job_id')->nullable(); // هنربطها بـ foreign key بعدين لما جدول jobs يتعمل
        $table->foreignId('payer_id')->constrained('users')->onDelete('cascade'); // مين اللي دفع
        $table->decimal('amount', 10, 2);
        $table->enum('method', ['cash', 'fawry', 'instapay', 'vodafone_cash', 'wallet']);
        $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
        $table->string('gateway_ref')->nullable(); // رقم مرجعي من بوابة الدفع
        $table->timestamp('paid_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
