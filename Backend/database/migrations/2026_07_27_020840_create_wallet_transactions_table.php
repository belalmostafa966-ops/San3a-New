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
    Schema::create('wallet_transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('wallet_id')->constrained()->onDelete('cascade'); // ربط الحركة بالمحفظة
        $table->enum('type', ['deposit', 'withdrawal', 'fee_hold', 'refund']); // نوع الحركة
        $table->decimal('amount', 10, 2); // قيمة الحركة
        $table->string('description')->nullable(); // وصف للحركة (مثال: رسوم معاينة)
        $table->string('reference_id')->nullable(); // رقم مرجعي لو الحركة مربوطة بطلب معين
        $table->timestamps();
    });
}
};
