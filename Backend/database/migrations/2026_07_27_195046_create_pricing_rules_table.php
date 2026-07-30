<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();

            // كل قاعدة تسعير مرتبطة بمهنة معينة
            $table->foreignId('profession_id')
                ->constrained('professions')
                ->cascadeOnDelete();

            // السعر الأساسي قبل أي إضافات
            $table->decimal('baseline_price', 10, 2);

            // معامل الوقت (مثلاً: شغل بالليل أو الجمعة سعره مضاعف)
            // بنخزنه كـ JSON عشان يقبل قيم متعددة زي {"night": 1.2, "weekend": 1.15}
            $table->json('time_multiplier_json')->nullable();

            // معامل المكان/المنطقة (مثلاً: مناطق بعيدة سعرها أعلى)
            $table->json('geo_multiplier_json')->nullable();

            // هامش ربح المنصة (نسبة أو مبلغ ثابت فوق السعر)
            $table->decimal('platform_margin', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
