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
    Schema::create('visit_fee_holds', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('job_request_id');
        $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
        $table->decimal('amount', 10, 2)->default(50.00); // TODO: قيمة مؤقتة (placeholder) - محتاجة تأكيد نهائي من البيزنس
        $table->enum('status', ['held', 'confirmed', 'released'])->default('held');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_fee_holds');
    }
};
