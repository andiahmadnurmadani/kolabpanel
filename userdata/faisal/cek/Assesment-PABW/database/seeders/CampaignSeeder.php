<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Periksa apakah data dummy sudah ada dengan memeriksa title
        $existingTitles = [
            'Bantu Anak Pintar Bersekolah',
            'Peduli Korban Bencana Alam',
            'Lindungi Hutan Lindung'
        ];

        $existingCampaigns = \DB::table('campaigns')->whereIn('title', $existingTitles)->get();

        // Jika data dummy belum ada, tambahkan
        if ($existingCampaigns->count() === 0) {
            \DB::table('campaigns')->insert([
                [
                    'title' => 'Bantu Anak Pintar Bersekolah',
                    'description' => 'Kami mencari dana untuk membantu anak-anak berprestasi dari keluarga kurang mampu agar bisa melanjutkan pendidikan.',
                    'image' => 'https://example.com/images/education-campaign.jpg',
                    'target_amount' => 50000000,
                    'current_amount' => 15000000,
                    'end_date' => '2026-06-30',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Peduli Korban Bencana Alam',
                    'description' => 'Bantu meringankan beban saudara-saudara kita yang terkena bencana alam dengan donasi terbaik Anda.',
                    'image' => 'https://example.com/images/disaster-relief.jpg',
                    'target_amount' => 100000000,
                    'current_amount' => 45000000,
                    'end_date' => '2026-03-15',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Lindungi Hutan Lindung',
                    'description' => 'Dukung upaya konservasi hutan dan satwa liar dengan berdonasi untuk program perlindungan lingkungan.',
                    'image' => 'https://example.com/images/forest-protection.jpg',
                    'target_amount' => 75000000,
                    'current_amount' => 30000000,
                    'end_date' => '2026-05-20',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
