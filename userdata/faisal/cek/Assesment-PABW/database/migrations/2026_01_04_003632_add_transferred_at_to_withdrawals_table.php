<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('withdrawals', function (Blueprint $table) {
        // Menambahkan kolom transferred_at (boleh kosong)
        if (!Schema::hasColumn('withdrawals', 'transferred_at')) {
            $table->timestamp('transferred_at')->nullable()->after('status');
        }
    });
}

public function down()
{
    Schema::table('withdrawals', function (Blueprint $table) {
        $table->dropColumn('transferred_at');
    });
}
};
