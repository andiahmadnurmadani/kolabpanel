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
        Schema::create('folder_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->constrained()->onDelete('cascade'); // Folder yang dibagikan
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User yang diberi akses
            $table->enum('permission', ['view', 'edit'])->default('view'); // Tipe akses
            $table->timestamps();

            // Unique constraint: satu user hanya bisa punya satu akses per folder
            $table->unique(['folder_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folder_user');
    }
};
