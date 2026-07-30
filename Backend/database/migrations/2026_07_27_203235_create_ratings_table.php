<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id');
            $table->foreignId('rated_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('rated_user_id')->constrained('users')->onDelete('cascade');
            $table->string('direction');
            $table->integer('score');
            $table->integer('behavior_score')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};