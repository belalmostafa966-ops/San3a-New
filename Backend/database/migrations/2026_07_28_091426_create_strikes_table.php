<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('strikes', function (Blueprint $table) {
            $table->id();

            // اليوزر اللي اخد الإنذار
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // الشغلانة اللي الإنذار صدر بسببها (اختياري، ممكن يكون إنذار عام مش مرتبط بشغلانة معينة)
            $table->foreignId('job_id')
                ->nullable()
                ->constrained('jobs')
                ->nullOnDelete();

            // سبب الإنذار
            $table->text('reason');

            // العقوبة اللي اتطبقت (مثلاً: تنبيه، إيقاف مؤقت، خصم نقط)
            $table->string('penalty_applied')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('strikes');
    }
};
