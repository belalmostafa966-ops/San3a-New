<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('craftsman_id')->constrained('craftsman_profiles')->onDelete('cascade');
            $table->decimal('price_quote', 10, 2);
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();

            $table->unique(['job_request_id', 'craftsman_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_proposals');
    }
};
