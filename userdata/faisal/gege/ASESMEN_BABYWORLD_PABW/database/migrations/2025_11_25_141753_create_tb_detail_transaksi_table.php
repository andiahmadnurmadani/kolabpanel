<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_detail_transaksi', function (Blueprint $table) {
            $table->id('id_detail_transaksi');

            $table->unsignedBigInteger('id_transaksi');
            $table->unsignedBigInteger('id_produk');

            $table->integer('quantity');
            $table->integer('subtotal');

            $table->timestamps();

            // foreign key (opsional)
            $table->foreign('id_transaksi')->references('id')->on('tb_transaksi')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_detail_transaksi');
    }
};