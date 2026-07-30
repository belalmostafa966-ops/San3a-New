<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs_records')->onDelete('cascade');
            $table->foreignId('opened_by')->constrained('users');
            $table->text('reason');
            $table->text('resolution')->nullable();
            $table->enum('status', ['open', 'under_review', 'resolved'])->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
