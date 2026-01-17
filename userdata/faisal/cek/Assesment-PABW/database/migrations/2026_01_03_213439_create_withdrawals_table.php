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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            // Asumsi: withdrawal terhubung ke campaigns. 
            // Pastikan tabel 'campaigns' sudah ada. Jika belum, ganti ke 'users' atau hapus baris ini.
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            
            $table->decimal('amount', 15, 2);
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_holder_name');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable(); // Catatan dari admin saat approve/reject
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
