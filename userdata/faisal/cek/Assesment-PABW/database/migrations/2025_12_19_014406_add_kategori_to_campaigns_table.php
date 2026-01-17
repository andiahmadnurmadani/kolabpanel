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
        // PERBAIKAN: Cek apakah kolom 'kategori' sudah ada
        if (!Schema::hasColumn('campaigns', 'kategori')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->string('kategori')->nullable(); 
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // Cek sebelum menghapus untuk menghindari error saat rollback
            if (Schema::hasColumn('campaigns', 'kategori')) {
                $table->dropColumn('kategori');
            }
        });
    }
};