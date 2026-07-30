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
    Schema::create('commission_rules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('profession_id')->nullable()->constrained('professions')->onDelete('cascade');
        $table->decimal('min_percent', 5, 2)->default(10.00); // TODO: قيمة مؤقتة - محتاجة تأكيد نهائي من البيزنس
        $table->decimal('max_percent', 5, 2)->default(15.00); // TODO: قيمة مؤقتة - محتاجة تأكيد نهائي من البيزنس
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_rules');
    }
};
