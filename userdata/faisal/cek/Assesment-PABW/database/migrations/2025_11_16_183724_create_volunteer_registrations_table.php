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
        Schema::create('volunteer_registrations', function (Blueprint $table) {
            $table->id();

            // Buat nyambungin ke user (kalo dia udah login)
            // Kalo dia daftar sbg 'guest', ID ini bakal 'null'
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // Data Pendaftar
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->text('motivation'); // Alasan/motivasi dia
            $table->string('skills')->nullable(); // Keahlian (opsional)

            // Status pendaftaran (ini buat admin)
            $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected'

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_registrations');
    }
};
