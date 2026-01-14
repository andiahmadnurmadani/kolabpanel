<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user dan folder untuk testing
        $user = User::first();
        $folder = Folder::where('name', 'Dokumen Kelompok')->first();

        if (!$user || !$folder) {
            $this->command->error('User or Folder not found. Please run UserSeeder and FolderSeeder first.');
            return;
        }

        // Dummy files - dalam production, ini akan diupload real files
        File::create([
            'user_id' => $user->id,
            'folder_id' => $folder->id,
            'name' => 'laporan_mingguan_' . time(),
            'original_name' => 'Laporan Mingguan.docx',
            'file_path' => 'materials/dummy/laporan_mingguan.docx',
            'file_type' => 'docx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'size' => 20971520, // 20 MB
        ]);

        File::create([
            'user_id' => $user->id,
            'folder_id' => $folder->id,
            'name' => 'presentasi_final_' . time(),
            'original_name' => 'Presentasi Final.pdf',
            'file_path' => 'materials/dummy/presentasi_final.pdf',
            'file_type' => 'pdf',
            'mime_type' => 'application/pdf',
            'size' => 15728640, // 15 MB
        ]);

        File::create([
            'user_id' => $user->id,
            'folder_id' => $folder->id,
            'name' => 'referensi_jurnal_' . time(),
            'original_name' => 'Referensi Jurnal.xlsx',
            'file_path' => 'materials/dummy/referensi_jurnal.xlsx',
            'file_type' => 'xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'size' => 12582912, // 12 MB
        ]);

        // Update folder size
        $folder->updateSize();

        $this->command->info('Files created successfully!');
    }
}
