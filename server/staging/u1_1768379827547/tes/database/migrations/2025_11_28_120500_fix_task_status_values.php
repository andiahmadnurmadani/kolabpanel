<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure all legacy status values are normalized
        DB::table('tasks')->where('status', 'selesai')->update(['status' => 'completed']);
        DB::table('tasks')->where('status', 'in_progress')->update(['status' => 'pending']);
    }

    public function down(): void
    {
        // Revert 'completed' back to 'selesai' if needed
        DB::table('tasks')->where('status', 'completed')->update(['status' => 'selesai']);
        // We won't revert 'pending' back to 'in_progress' intentionally
    }
};
