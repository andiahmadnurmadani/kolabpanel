<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('volunteer_applications', function (Blueprint $table) {
            $table->id();
            // Relasi ke User (Siapa yang daftar)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Relasi ke Campaign (Daftar ke acara apa)
            $table->foreignId('volunteer_campaign_id')->constrained('volunteer_campaigns')->onDelete('cascade');

            // Data Tambahan
            $table->text('alamat');
            $table->text('alasan_bergabung');
            $table->string('posisi_dilamar'); // Misal: Logistik, Medis, dll
            $table->string('cv_path'); // Lokasi file CV

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_applications');
    }
};
