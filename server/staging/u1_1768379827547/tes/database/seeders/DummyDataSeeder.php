<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Note;
use App\Models\Task;
use App\Models\Activity;
use App\Models\Folder;
use App\Models\File;
use App\Models\KontenStatis;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create or get user
        $user = User::first() ?? User::create([
            'name' => 'Demo User',
            'email' => 'demo@notezque.test',
            'password' => Hash::make('password123'),
        ]);

        // 2. Clear or keep existing? Let's just add 8 more for each as requested.
        
        // --- 8 USERS ---
        User::factory()->count(8)->create();
        $this->command->info('8 Users created.');

        // --- 8 NOTES ---
        $noteTitles = [
            'Rencana Liburan Akhir Tahun',
            'Daftar Belanja Mingguan',
            'Ide Bisnis Startup Kreatif',
            'Catatan Meeting Project X',
            'Ringkasan Buku Atomic Habits',
            'Resep Masakan Nusantara',
            'Target Olahraga 2025',
            'Draft Artikel Blog Baru'
        ];

        foreach ($noteTitles as $title) {
            Note::create([
                'user_id' => $user->id,
                'title' => $title,
                'content' => "Ini adalah konten dummy untuk catatan: " . $title . ".\n\n" . Str::random(100),
            ]);
        }
        $this->command->info('8 Notes created.');

        // --- 8 TASKS ---
        $taskData = [
            ['Tugas Matematika', 'Selesaikan soal bab 4', 'high', 'pending'],
            ['Laporan Praktikum', 'Tulis hasil pengamatan', 'medium', 'pending'],
            ['Bayar UKT', 'Transfer via Virtual Account', 'high', 'completed'],
            ['Belanja Bulanan', 'Beli sabun, beras, minyak', 'low', 'pending'],
            ['Service Motor', 'Ganti oli dan cek rem', 'medium', 'in_progress'],
            ['Baca Jurnal', 'Cari referensi skripsi', 'medium', 'pending'],
            ['Update Portofolio', 'Tambah project terbaru', 'low', 'completed'],
            ['Siapkan Presentasi', 'Buat slide di Canva', 'high', 'pending'],
        ];

        foreach ($taskData as $t) {
            Task::create([
                'user_id' => $user->id,
                'title' => $t[0],
                'description' => $t[1],
                'priority' => $t[2],
                'status' => $t[3],
                'due_date' => Carbon::now()->addDays(rand(1, 14)),
                'completed_at' => $t[3] === 'completed' ? now() : null,
            ]);
        }
        $this->command->info('8 Tasks created.');

        // --- 8 ACTIVITIES ---
        $activityTitles = [
            'Workshop Laravel',
            'Webinar AI Trends',
            'Meeting Organisasi',
            'Olahraga Sore',
            'Bimbingan Skripsi',
            'Family Dinner',
            'Kursus Bahasa Inggris',
            'Streaming Film Baru'
        ];

        foreach ($activityTitles as $title) {
            Activity::create([
                'user_id' => $user->id,
                'title' => $title,
                'desk' => 'Deskripsi untuk kegiatan: ' . $title,
                'date' => Carbon::now()->addDays(rand(-7, 7)),
                'time' => '10:00',
                'reminder' => '30',
                'status' => rand(0, 1) ? 'pending' : 'selesai',
            ]);
        }
        $this->command->info('8 Activities created.');

        // --- 8 FOLDERS ---
        $folderNames = [
            'Project Alpha',
            'Dokumen Pribadi',
            'Materi Kuliah',
            'Foto Kenangan',
            'Arsip Lama',
            'Draft Design',
            'Finance',
            'Workout Plan'
        ];

        $folders = [];
        foreach ($folderNames as $name) {
            $folders[] = Folder::create([
                'user_id' => $user->id,
                'name' => $name,
                'color' => ['blue', 'red', 'green', 'yellow', 'purple', 'indigo', 'pink', 'gray'][rand(0, 7)],
                'size' => 0,
            ]);
        }
        $this->command->info('8 Folders created.');

        // --- 8 FILES ---
        $fileExtensions = ['pdf', 'doc', 'jpg', 'png', 'xlsx', 'txt'];
        for ($i = 1; $i <= 8; $i++) {
            $ext = $fileExtensions[rand(0, 5)];
            $folder = $folders[rand(0, 7)];
            File::create([
                'user_id' => $user->id,
                'folder_id' => $folder->id,
                'name' => 'File_Dummy_' . $i . '_' . time(),
                'original_name' => 'Dokumen_' . $i . '.' . $ext,
                'file_path' => 'materials/dummy/file_' . $i . '.' . $ext,
                'file_type' => $ext,
                'mime_type' => 'application/octet-stream',
                'size' => rand(1024, 1024000),
            ]);
        }
        $this->command->info('8 Files created.');

        // --- 8 KONTEN STATIS ---
        $kontenKeys = [
            'dummy_info_1',
            'dummy_info_2',
            'dummy_info_3',
            'dummy_info_4',
            'dummy_active_status',
            'dummy_system_version',
            'dummy_maintenance_notice',
            'dummy_announcement'
        ];

        foreach ($kontenKeys as $key) {
            KontenStatis::updateOrCreate(
                ['key' => $key],
                [
                    'value' => 'Nilai dummy untuk ' . $key,
                    'type' => 'text',
                ]
            );
        }
        $this->command->info('8 Konten Statis created.');
    }
}
