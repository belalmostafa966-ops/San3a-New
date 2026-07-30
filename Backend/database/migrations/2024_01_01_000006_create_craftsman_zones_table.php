<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('craftsman_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('craftsman_id')->constrained('craftsman_profiles')->onDelete('cascade');
            $table->foreignId('zone_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['craftsman_id', 'zone_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('craftsman_zones');
    }
};
