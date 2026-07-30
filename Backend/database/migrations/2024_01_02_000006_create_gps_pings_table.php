<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gps_pings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs_records')->onDelete('cascade');
            $table->foreignId('craftsman_id')->constrained('craftsman_profiles');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['job_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gps_pings');
    }
};
