<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TaskSeeder extends Seeder
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

        $tasks = [
            // Tugas yang belum selesai
            [
                'title' => 'Selesaikan Tugas Pemrograman Web',
                'description' => 'Buat aplikasi CRUD dengan Laravel. Fitur: Create, Read, Update, Delete untuk data mahasiswa.',
                'due_date' => Carbon::now()->addDays(3),
                'priority' => 'high',
                'status' => 'pending',
                'completed_at' => null,
            ],
            [
                'title' => 'Baca Materi Database Chapter 5',
                'description' => 'Pelajari tentang normalisasi database dan relasi tabel.',
                'due_date' => Carbon::now()->addDays(5),
                'priority' => 'medium',
                'status' => 'pending',
                'completed_at' => null,
            ],
            [
                'title' => 'Presentasi Proyek Akhir',
                'description' => 'Siapkan slide presentasi dan demo aplikasi untuk mata kuliah Rekayasa Perangkat Lunak.',
                'due_date' => Carbon::now()->addDays(7),
                'priority' => 'high',
                'status' => 'pending',
                'completed_at' => null,
            ],
            
            // Tugas yang sudah selesai
            [
                'title' => 'Quiz Algoritma dan Pemrograman',
                'description' => 'Quiz online tentang struktur data dan kompleksitas algoritma.',
                'due_date' => Carbon::now()->subDays(2),
                'priority' => 'high',
                'status' => 'completed',
                'completed_at' => Carbon::now()->subDays(3),
            ],
            [
                'title' => 'Diskusi Kelompok Proyek',
                'description' => 'Meeting dengan tim untuk membahas pembagian tugas proyek akhir.',
                'due_date' => Carbon::now()->subDays(5),
                'priority' => 'medium',
                'status' => 'completed',
                'completed_at' => Carbon::now()->subDays(6),
            ],
            [
                'title' => 'Install Development Tools',
                'description' => 'Install Laravel, Composer, dan tools development lainnya.',
                'due_date' => Carbon::now()->subDays(7),
                'priority' => 'high',
                'status' => 'completed',
                'completed_at' => Carbon::now()->subDays(8),
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create([
                'user_id' => $user->id,
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'due_date' => $taskData['due_date'],
                'priority' => $taskData['priority'],
                'status' => $taskData['status'],
                'completed_at' => $taskData['completed_at'],
            ]);
        }

        $this->command->info('Tasks seeded successfully!');
    }
}
