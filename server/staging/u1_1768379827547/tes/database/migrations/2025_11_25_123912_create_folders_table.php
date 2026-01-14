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
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Pemilik folder
            $table->foreignId('parent_id')->nullable()->constrained('folders')->onDelete('cascade'); // Folder induk (null = root)
            $table->string('name'); // Nama folder
            $table->string('color')->default('blue'); // Warna folder untuk UI
            $table->unsignedBigInteger('size')->default(0); // Total size file dalam bytes
            $table->timestamps();

            // Index untuk performa
            $table->index(['user_id', 'parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folders');
    }
};
