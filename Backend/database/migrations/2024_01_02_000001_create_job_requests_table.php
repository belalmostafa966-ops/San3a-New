<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('profession_id')->constrained();
            $table->text('description'); // إجباري حسب الـ PDF
            $table->foreignId('zone_id')->constrained();
            $table->string('address');
            $table->timestamp('preferred_time')->nullable();
            $table->enum('status', [
                'open',            // منشور، مستني proposals
                'proposal_accepted', // العميل اختار صنايعي
                'in_progress',
                'completed',
                'cancelled',
            ])->default('open');
            $table->enum('visit_fee_status', ['unpaid', 'held', 'consumed', 'refunded'])->default('unpaid');
            $table->timestamps();

            $table->index(['profession_id', 'zone_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_requests');
    }
};
