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
        // LOGIKA PERBAIKAN:
        // Cek apakah kolom 'yayasan' sudah ada di tabel 'campaigns'.
        // Jika belum ada, baru tambahkan. Jika sudah ada, lewati.
        if (!Schema::hasColumn('campaigns', 'yayasan')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->string('yayasan')->nullable()->after('kategori');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            if (Schema::hasColumn('campaigns', 'yayasan')) {
                $table->dropColumn('yayasan');
            }
        });
    }
};