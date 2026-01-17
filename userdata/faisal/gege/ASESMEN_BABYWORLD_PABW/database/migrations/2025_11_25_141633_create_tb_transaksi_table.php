<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_transaksi', function (Blueprint $table) {
            $table->id(); // primary key
            $table->string('kode_transaksi', 100);
            $table->unsignedBigInteger('id_user'); 
            $table->unsignedBigInteger('id_admin')->nullable();
            $table->unsignedBigInteger('id_voucher')->nullable();

            $table->decimal('total_harga', 10, 2);

            $table->string('metode_pembayaran', 30);

            $table->enum('status_order', ['menunggu','diproses','dikemas','dikirim'])
                ->default('menunggu');

            $table->enum('status_pembayaran', ['belum bayar','sudah bayar','gagal bayar'])
                ->default('belum bayar');

            $table->text('address')->nullable();
            $table->string('city', 255)->nullable();
            $table->string('country', 255)->nullable();
            $table->integer('post_code')->nullable();
            $table->text('order_note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_transaksi');
    }
};