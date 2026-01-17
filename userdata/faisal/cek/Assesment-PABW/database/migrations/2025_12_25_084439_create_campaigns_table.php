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
        // PERBAIKAN: Logika "Cek Dulu"
        // Karena tabel campaigns ternyata sudah ada (akibat urutan migrasi yang kacau),
        // kita skip pembuatan tabel ini jika sudah terdeteksi.
        if (!Schema::hasTable('campaigns')) {
            Schema::create('campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->string('image')->nullable();
                $table->decimal('target_amount', 15, 2);
                $table->decimal('current_amount', 15, 2)->default(0);
                $table->date('end_date');
                $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};