<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize legacy statuses to canonical values
        DB::table('tasks')->where('status', 'selesai')->update(['status' => 'completed']);
        DB::table('tasks')->where('status', 'in_progress')->update(['status' => 'pending']);
    }

    public function down(): void
    {
        // Revert only the 'completed' normalization back to 'selesai'
        DB::table('tasks')->where('status', 'completed')->update(['status' => 'selesai']);
        // We intentionally do not revert 'pending' back to 'in_progress'
    }
};
