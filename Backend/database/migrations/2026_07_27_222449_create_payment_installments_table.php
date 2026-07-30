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
    Schema::create('payment_installments', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('job_id')->nullable(); // هنربطها بعدين لما جدول jobs يتعمل
        $table->integer('installment_number'); // رقم القسط (1، 2، 3...)
        $table->decimal('percentage', 5, 2); // النسبة من إجمالي المبلغ (مثلاً 40%)
        $table->decimal('amount', 10, 2); // القيمة الفعلية بالجنيه
        $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
        $table->timestamp('due_at')->nullable();
        $table->timestamp('paid_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_installments');
    }
};
