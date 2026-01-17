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
    Schema::create('donations', function (Blueprint $table) {
        $table->id();
        
        // Relasi ke User (Nullable karena donatur bisa jadi Guest/Tanpa Login)
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
        
        // Relasi ke Campaign (Wajib ada)
        // Pastikan nama tabel campaign Anda benar ('campaigns' atau 'volunteer_campaigns'?)
        // Asumsi saya nama tabelnya 'campaigns' berdasarkan standar Laravel
        $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade'); 

        $table->string('order_id')->unique();     // ID Transaksi Unik
        $table->decimal('amount', 15, 2);         // Jumlah donasi (Decimal biar aman)
        $table->string('status')->default('pending'); // pending, paid, failed
        $table->string('snap_token')->nullable(); // Token Midtrans
        
        // Data Donatur (Untuk Guest)
        $table->string('donor_name')->nullable();
        $table->string('donor_email')->nullable();
        
        $table->text('doa')->nullable();          // Pesan/Doa
        $table->boolean('is_anonymous')->default(false);
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
