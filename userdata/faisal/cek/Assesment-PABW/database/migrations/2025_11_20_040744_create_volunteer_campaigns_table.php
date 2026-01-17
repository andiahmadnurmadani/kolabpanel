<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('volunteer_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->text('deskripsi');

            $table->string('lokasi');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            // Kategori untuk label warna-warni
            $table->enum('kategori', ['Pendidikan', 'Bencana', 'Kesehatan', 'Lingkungan', 'Sosial']);

            // Sistem Kuota untuk Progress Bar
            $table->integer('kuota_total');
            $table->integer('kuota_terisi')->default(0);

            $table->enum('status', ['Aktif', 'Nonaktif', 'Selesai'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('volunteer_campaigns');
    }
};
