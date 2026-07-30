<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('craftsman_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('profession_id')->constrained();
            $table->integer('years_experience')->default(0);
            $table->text('bio')->nullable();
            $table->integer('jobs_completed_count')->default(0);
            $table->enum('verification_tier', ['basic', 'verified'])->default('basic');
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->decimal('behavior_score', 3, 1)->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('craftsman_profiles');
    }
};
