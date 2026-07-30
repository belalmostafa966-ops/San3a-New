<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_status_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs_records')->onDelete('cascade');
            $table->string('event_type'); // مثال: proposal_accepted, started, otp_confirmed, cancelled...
            $table->json('meta_json')->nullable();
            $table->timestamps();

            $table->index(['job_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_status_events');
    }
};
