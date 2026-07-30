<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'phone_number')) {
            $table->string('phone_number')->nullable()->unique();
        }
        if (!Schema::hasColumn('users', 'role')) {
            $table->string('role')->default('user');
        }
        if (!Schema::hasColumn('users', 'is_active')) {
            $table->boolean('is_active')->default(true);
        }
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['phone_number', 'role', 'is_active']);
    });
}
};
