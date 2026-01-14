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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Pemilik file
            $table->foreignId('folder_id')->nullable()->constrained()->onDelete('cascade'); // Folder tempat file (null = root)
            $table->string('name'); // Nama file yang di-generate (unique)
            $table->string('original_name'); // Nama file asli dari user
            $table->string('file_path'); // Path file di storage
            $table->string('file_type'); // Tipe file (pdf, docx, xlsx, jpg, png)
            $table->string('mime_type'); // MIME type
            $table->unsignedBigInteger('size'); // Ukuran file dalam bytes
            $table->timestamps();

            // Index untuk performa
            $table->index(['user_id', 'folder_id']);
            $table->index('file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
