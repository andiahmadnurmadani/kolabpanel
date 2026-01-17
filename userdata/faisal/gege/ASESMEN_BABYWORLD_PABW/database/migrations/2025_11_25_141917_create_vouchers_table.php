<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_voucher', function (Blueprint $table) {
            $table->id(); // id

            $table->string('kode_voucher', 50)->unique();

            // silakan sesuaikan enum-nya persis seperti DB lama
            $table->enum('jenis_voucher', [
                'Gratis Ongkir',
                'Diskon & Cashback',
                'Kirim...'
            ])->default('Diskon & Cashback');

            $table->decimal('diskon', 10, 2); // contoh: 15.00 (15%)

            $table->text('deskripsi')->nullable();

            $table->date('tanggal_berlaku');
            $table->date('tanggal_expired');

            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');

            $table->integer('quantity')->default(0);

            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_voucher');
    }
};