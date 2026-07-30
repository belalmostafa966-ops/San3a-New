<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();

            // الصنايعي اللي دافع عشان الظهور المميز
            $table->foreignId('craftsman_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // المنطقة المستهدفة (اختياري، لو فاضي يبقى الترويج شامل كل المناطق)
            $table->foreignId('zone_id')
                ->nullable()
                ->constrained('zones')
                ->nullOnDelete();

            // بداية ونهاية فترة الترويج
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');

            // المبلغ المدفوع
            $table->decimal('amount_paid', 10, 2);

            // حالة الترويج: نشط، منتهي، ملغي
            $table->string('status')->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
