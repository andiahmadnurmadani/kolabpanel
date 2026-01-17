<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('withdrawals', function (Blueprint $table) {

        // 1. Tambah user_id (Admin yang input) jika belum ada
        if (!Schema::hasColumn('withdrawals', 'user_id')) {
            $table->foreignId('user_id')->nullable()->after('campaign_id')->constrained('users')->onDelete('set null');
        }

        // 2. Tambah transferred_at (Waktu transaksi) jika belum ada
        if (!Schema::hasColumn('withdrawals', 'transferred_at')) {
            $table->timestamp('transferred_at')->nullable()->after('status');
        }

        // 3. Tambah proof_file (Bukti Nota) jika belum ada
        if (!Schema::hasColumn('withdrawals', 'proof_file')) {
            $table->string('proof_file')->nullable()->after('transferred_at');
        }

        // 4. Tambah admin_note (Catatan) jika belum ada
        if (!Schema::hasColumn('withdrawals', 'admin_note')) {
            $table->text('admin_note')->nullable()->after('proof_file');
        }
    });
}

public function down()
{
    Schema::table('withdrawals', function (Blueprint $table) {
        // Hapus kolom jika rollback
        $table->dropColumn(['user_id', 'transferred_at', 'proof_file', 'admin_note']);
    });
}
};
