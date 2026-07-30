<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standardized_service_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('profession_id')
                ->constrained('professions')
                ->cascadeOnDelete();

            $table->string('title');

            $table->decimal('fixed_price', 10, 2);

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standardized_service_prices');
    }
};
