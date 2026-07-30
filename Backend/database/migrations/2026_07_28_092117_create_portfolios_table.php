<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();

            // الصنايعي صاحب المعرض
            $table->foreignId('craftsman_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // عنوان قصير للشغلانة (مثلاً: "تركيب مطبخ")
            $table->string('title');

            // مسار صورة "قبل"
            $table->string('before_image')->nullable();

            // مسار صورة "بعد"
            $table->string('after_image')->nullable();

            // مصدر الصورة: رفعها الصنايعي بنفسه، أو مأخوذة من شغلانة فعلية على المنصة
            $table->string('source')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
