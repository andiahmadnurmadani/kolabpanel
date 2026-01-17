<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DonationTransaction;
use App\Models\User;
use App\Models\Campaign;
use Illuminate\Support\Str;

class DonationTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get sample users and campaigns to link transactions
        $users = User::all();
        $campaigns = Campaign::all();

        if ($users->isEmpty() || $campaigns->isEmpty()) {
            // If no users or campaigns exist, skip seeding
            return;
        }

        // Create sample donation transactions
        foreach (range(1, 10) as $index) {
            DonationTransaction::create([
                'order_id' => 'ORD-' . strtoupper(Str::random(10)),
                'amount' => rand(50000, 5000000), // Random donation amount between 50k and 5M
                'donor_name' => fake()->name(),
                'donor_email' => fake()->unique()->safeEmail(),
                'donor_phone' => '+62' . fake()->numerify('###########'),
                'user_id' => $users->random()->id, // Link to random user
                'payment_method' => fake()->randomElement(['bank_transfer', 'e_wallet', 'qris']),
                'status' => fake()->randomElement(['AWAITING_TRANSFER', 'PENDING_VERIFICATION', 'VERIFIED', 'CANCELLED']),
                'campaign_id' => $campaigns->count() > 0 ? $campaigns->random()->id : null,
                'transfer_deadline' => now()->addDays(rand(1, 7)),
                'bank_account_name' => 'Organisasi Amal DonGiv',
                'bank_account_number' => fake()->numerify('####################'),
                'bank_name' => fake()->randomElement(['Bank Mandiri', 'BCA', 'BNI', 'Mandiri']),
                'proof_of_transfer_path' => null, // Will be filled in when user uploads
            ]);
        }

        // Create some additional transactions with VERIFIED status to award coins
        foreach (range(1, 5) as $index) {
            DonationTransaction::create([
                'order_id' => 'ORD-' . strtoupper(Str::random(10)),
                'amount' => rand(100000, 1000000),
                'donor_name' => fake()->name(),
                'donor_email' => fake()->unique()->safeEmail(),
                'donor_phone' => '+62' . fake()->numerify('###########'),
                'user_id' => $users->random()->id, // Link to random user
                'payment_method' => fake()->randomElement(['bank_transfer', 'e_wallet', 'qris']),
                'status' => 'VERIFIED', // Mark as verified to award coins
                'campaign_id' => $campaigns->count() > 0 ? $campaigns->random()->id : null,
                'transfer_deadline' => now()->subDays(rand(1, 3)), // Past deadline so it's completed
                'bank_account_name' => 'Organisasi Amal DonGiv',
                'bank_account_number' => fake()->numerify('####################'),
                'bank_name' => fake()->randomElement(['Bank Mandiri', 'BCA', 'BNI', 'Mandiri']),
                'proof_of_transfer_path' => 'transfer-proofs/sample-proof.jpg', // Sample proof path
            ]);
        }
    }
}
