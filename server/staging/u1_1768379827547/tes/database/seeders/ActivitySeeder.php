<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user pertama atau buat jika belum ada
        $user = User::first();
        
        if (!$user) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        $activities = [
            // Kegiatan akademik
            [
                'title' => 'Konsultasi Dosen Pembimbing',
                'desk' => 'Progress report proyek akhir semester',
                'date' => Carbon::now()->addDays(3),
                'time' => '14:00',
                'reminder' => '60',
                'status' => 'pending',
            ],
            
            // Kegiatan organisasi
            [
                'title' => 'Rapat HMTI',
                'desk' => 'Rapat koordinasi persiapan tech talk',
                'date' => Carbon::now()->addDays(2),
                'time' => '16:00',
                'reminder' => '60',
                'status' => 'pending',
            ],
            
            // Meeting proyek
            [
                'title' => 'Sprint Planning Meeting',
                'desk' => 'Planning sprint 2 proyek aplikasi e-learning',
                'date' => Carbon::now()->addDays(1),
                'time' => '15:00',
                'reminder' => '30',
                'status' => 'pending',
            ],
            
            
            // Kegiatan personal
            [
                'title' => 'Olahraga Pagi',
                'desk' => 'Jogging di taman kampus',
                'date' => Carbon::now()->addDays(1),
                'time' => '06:00',
                'reminder' => '15',
                'status' => 'pending',
            ],
            
            // Kegiatan yang sudah lewat (selesai)
            [
                'title' => 'Kuliah Pemrograman Web',
                'desk' => 'Pertemuan 9: Laravel Eloquent ORM',
                'date' => Carbon::now()->subDays(2),
                'time' => '08:00',
                'reminder' => '30',
                'status' => 'selesai',
            ],
        ];

        foreach ($activities as $activityData) {
            Activity::create([
                'user_id' => $user->id,
                'title' => $activityData['title'],
                'desk' => $activityData['desk'],
                'date' => $activityData['date'],
                'time' => $activityData['time'],
                'reminder' => $activityData['reminder'],
                'status' => $activityData['status'],
            ]);
        }

        $this->command->info('Activities seeded successfully!');
    }
}
