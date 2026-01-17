<?php

namespace Database\Seeders;

// Removed WithoutModelEvents to allow factories to work properly
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Removed WithoutModelEvents to allow factories to work properly

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the admin user seeder to create your specific users
        $this->call(AdminUserSeeder::class);

        // Create a sample donatur user for testing
        \App\Models\User::factory()->create([
            'name' => 'Test Donatur',
            'email' => 'donatur@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'donatur',
        ]);

        $this->call([
            VolunteerCampaignSeeder::class,
            DonationTransactionSeeder::class,
        ]);
    }
 
    
}
