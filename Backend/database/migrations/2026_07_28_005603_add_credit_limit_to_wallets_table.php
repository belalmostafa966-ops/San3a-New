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
    Schema::table('wallets', function (Blueprint $table) {
        if (!Schema::hasColumn('wallets', 'credit_limit')) {
            // TODO: القيمة الافتراضية دي placeholder - محتاجة تأكيد نهائي من البيزنس
            $table->decimal('credit_limit', 10, 2)->default(-500.00)->after('held_amount');
        }
    });
}

public function down(): void
{
    Schema::table('wallets', function (Blueprint $table) {
        $table->dropColumn('credit_limit');
    });
}
};
