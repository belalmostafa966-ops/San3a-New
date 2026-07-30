<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();

            // الصنايعي اللي حصل على الوسام
            $table->foreignId('craftsman_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // نوع الوسام (مثلاً: "أفضل صنايعي الشهر"، "100 شغلانة")
            $table->string('badge_type');

            // تاريخ الحصول على الوسام
            $table->timestamp('awarded_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
