<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing records that have 'midtrans' as payment method to 'bank_transfer'
        DB::table('donation_transactions')
            ->where('payment_method', 'midtrans')
            ->update(['payment_method' => 'bank_transfer']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'bank_transfer' back to 'midtrans' for records that were originally midtrans
        // This is a simplified rollback - in a real scenario you might need to track original values
    }
};
