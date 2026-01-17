<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Volunteer;
use App\Models\VolunteerCampaign;
use Illuminate\Support\Facades\Hash;

class VolunteerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statusOptions = ['pending', 'disetujui', 'ditolak'];
        $campaigns = VolunteerCampaign::all();

        for ($i = 0; $i < 10; $i++) {
            Volunteer::create([
                'nama_lengkap' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'whatsapp' => fake()->phoneNumber(),
                'motivasi' => fake()->paragraph(),
                'keahlian' => fake()->sentence(),
                'status_verifikasi' => $statusOptions[array_rand($statusOptions)],
                'volunteer_campaign_id' => $campaigns->isNotEmpty() ? $campaigns->random()->id : null,
            ]);
        }
    }
}