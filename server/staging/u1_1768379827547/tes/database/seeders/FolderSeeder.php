<?php

namespace Database\Seeders;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Seeder;

class FolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user pertama untuk testing
        $user = User::first();

        if (!$user) {
            $this->command->error('No user found. Please run UserSeeder first.');
            return;
        }

        // Root Folders
        $folderDesain = Folder::create([
            'user_id' => $user->id,
            'parent_id' => null,
            'name' => 'Materi Desain',
            'color' => 'blue',
            'size' => 0,
        ]);

        $folderDokumen = Folder::create([
            'user_id' => $user->id,
            'parent_id' => null,
            'name' => 'Dokumen Kelompok',
            'color' => 'green',
            'size' => 0,
        ]);

        $folderTugas = Folder::create([
            'user_id' => $user->id,
            'parent_id' => null,
            'name' => 'Tugas Kuliah',
            'color' => 'purple',
            'size' => 0,
        ]);

        // Subfolder dalam Materi Desain
        Folder::create([
            'user_id' => $user->id,
            'parent_id' => $folderDesain->id,
            'name' => 'UI/UX',
            'color' => 'indigo',
            'size' => 0,
        ]);

        Folder::create([
            'user_id' => $user->id,
            'parent_id' => $folderDesain->id,
            'name' => 'Grafis',
            'color' => 'pink',
            'size' => 0,
        ]);

        // Subfolder dalam Tugas Kuliah
        Folder::create([
            'user_id' => $user->id,
            'parent_id' => $folderTugas->id,
            'name' => 'Semester 5',
            'color' => 'yellow',
            'size' => 0,
        ]);

        $this->command->info('Folders created successfully!');
    }
}
