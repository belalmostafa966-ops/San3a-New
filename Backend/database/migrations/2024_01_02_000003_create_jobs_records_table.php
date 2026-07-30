<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs_records', function (Blueprint $table) {
            // اسم الجدول jobs_records مش jobs عشان يتعارض مع جدول الـ queue الافتراضي بتاع Laravel
            $table->id();
            $table->foreignId('job_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('craftsman_id')->constrained('craftsman_profiles');
            $table->foreignId('client_id')->constrained('users');
            $table->enum('status', [
                'accepted',
                'on_the_way',
                'in_progress',
                'completed',
                'cancelled',
            ])->default('accepted');
            $table->timestamp('started_at')->nullable();
            $table->string('otp_code')->nullable();
            $table->timestamp('otp_confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->enum('cancelled_by', ['client', 'craftsman', 'system'])->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs_records');
    }
};
