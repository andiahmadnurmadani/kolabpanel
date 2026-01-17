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
        // Drop the snap_token column from the donations table if it exists
        if (Schema::hasColumn('donations', 'snap_token')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->dropColumn('snap_token');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the snap_token column if needed for rollback
        Schema::table('donations', function (Blueprint $table) {
            $table->string('snap_token')->nullable(); // Token Midtrans
        });
    }
};
