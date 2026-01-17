<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VolunteerCampaign;
use Faker\Factory as Faker;

class VolunteerCampaignSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        // Create meaningful volunteer campaigns with Indonesian locations and dates
        $campaigns = [
            [
                'judul' => 'Relawan Bencana Alam di Yogyakarta',
                'lokasi' => 'Yogyakarta',
                'tanggal_mulai' => $faker->dateTimeBetween('-2 months', 'now'),
                'tanggal_selesai' => $faker->dateTimeBetween('now', '+2 months'),
                'status' => 'Aktif',
            ],
            [
                'judul' => 'Relawan Pendidikan di Papua',
                'lokasi' => 'Jayapura',
                'tanggal_mulai' => $faker->dateTimeBetween('-3 months', '-1 month'),
                'tanggal_selesai' => $faker->dateTimeBetween('+1 month', '+3 months'),
                'status' => 'Aktif',
            ],
            [
                'judul' => 'Relawan Lingkungan Hidup Jakarta',
                'lokasi' => 'Jakarta',
                'tanggal_mulai' => $faker->dateTimeBetween('-1 month', 'now'),
                'tanggal_selesai' => $faker->dateTimeBetween('now', '+1 month'),
                'status' => 'Aktif',
            ],
            [
                'judul' => 'Relawan Kesehatan di Bali',
                'lokasi' => 'Denpasar',
                'tanggal_mulai' => $faker->dateTimeBetween('-2 months', '-1 month'),
                'tanggal_selesai' => $faker->dateTimeBetween('+1 month', '+2 months'),
                'status' => 'Nonaktif',
            ],
            [
                'judul' => 'Relawan Gempa Lombok',
                'lokasi' => 'Lombok',
                'tanggal_mulai' => $faker->dateTimeBetween('-6 months', '-4 months'),
                'tanggal_selesai' => $faker->dateTimeBetween('-3 months', '-1 month'),
                'status' => 'Nonaktif',
            ],
            [
                'judul' => 'Relawan Pembersihan Sungai di Bandung',
                'lokasi' => 'Bandung',
                'tanggal_mulai' => $faker->dateTimeBetween('now', '+1 week'),
                'tanggal_selesai' => $faker->dateTimeBetween('+1 month', '+2 months'),
                'status' => 'Aktif',
            ],
            [
                'judul' => 'Relawan Bantuan Korban Banjir Surabaya',
                'lokasi' => 'Surabaya',
                'tanggal_mulai' => $faker->dateTimeBetween('-1 month', 'now'),
                'tanggal_selesai' => $faker->dateTimeBetween('+2 weeks', '+1 month'),
                'status' => 'Aktif',
            ],
            [
                'judul' => 'Relawan Gerakan Literasi di Kalimantan',
                'lokasi' => 'Pontianak',
                'tanggal_mulai' => $faker->dateTimeBetween('-4 months', '-2 months'),
                'tanggal_selesai' => $faker->dateTimeBetween('+1 month', '+4 months'),
                'status' => 'Aktif',
            ],
        ];

        foreach ($campaigns as $campaign) {
            VolunteerCampaign::create([
                'judul' => $campaign['judul'],
                'slug' => \Illuminate\Support\Str::slug($campaign['judul']),
                'lokasi' => $campaign['lokasi'],
                'tanggal_mulai' => $campaign['tanggal_mulai'],
                'tanggal_selesai' => $campaign['tanggal_selesai'],
                'status' => $campaign['status'],
                'deskripsi' => $faker->paragraph,
                'kategori' => $faker->randomElement(['Pendidikan', 'Bencana', 'Kesehatan', 'Lingkungan', 'Sosial']),
                'kuota_total' => $faker->numberBetween(10, 100),
                'kuota_terisi' => $faker->numberBetween(0, 10),
            ]);
        }
    }
}

