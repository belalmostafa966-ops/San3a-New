<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->unique()->after('id');
            $table->enum('role', ['client', 'craftsman', 'admin'])->default('client')->after('password');
            $table->enum('status', ['active', 'suspended'])->default('active')->after('role');
            $table->string('device_token')->nullable()->after('status');
        });

        // email موجود جاهز من Laravel الافتراضي، بس بنخليه اختياري
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'status', 'device_token']);
        });
    }
};
