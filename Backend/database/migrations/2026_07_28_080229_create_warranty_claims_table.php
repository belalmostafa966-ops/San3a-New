<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();

            // الشغلانة اللي المطالبة دي بتاعتها
            $table->foreignId('job_id')
                ->constrained('jobs')
                ->cascadeOnDelete();

            // وصف المشكلة اللي العميل بيشتكي منها
            $table->text('issue_description');

            // نوع المطالبة: ضمان جودة، أو ضرر عرضي
            $table->enum('claim_type', ['quality_warranty', 'accidental_damage']);

            // حالة المطالبة: قيد المراجعة، مقبولة، مرفوضة، إلخ
            $table->string('status')->default('pending');

            // القرار النهائي / إزاي اتحلت المشكلة
            $table->text('resolution')->nullable();

            // رقم مرجعي لو المطالبة اتحولت لشركة تأمين
            $table->string('insurance_ref')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranty_claims');
    }
};
