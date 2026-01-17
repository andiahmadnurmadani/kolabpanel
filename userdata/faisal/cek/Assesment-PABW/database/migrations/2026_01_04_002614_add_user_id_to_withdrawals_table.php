<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('withdrawals', function (Blueprint $table) {
        // Cek dulu biar gak error "Duplicate column"
        if (!Schema::hasColumn('withdrawals', 'user_id')) {
            $table->foreignId('user_id')->nullable()->after('campaign_id')->constrained('users')->onDelete('set null');
        }
    });
}

public function down()
{
    Schema::table('withdrawals', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}
};
