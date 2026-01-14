<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KontenStatis;

class KontenStatisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $konten = [
            [
                'key' => 'site_logo',
                'value' => '/logo.png',
                'type' => 'image',
            ],
            [
                'key' => 'site_name',
                'value' => 'Notezque',
                'type' => 'text',
            ],
            [
                'key' => 'site_tagline',
                'value' => 'Productivity Hub',
                'type' => 'text',
            ],
            [
                'key' => 'hero_title',
                'value' => 'Tingkatkan Produktivitas Belajar Anda',
                'type' => 'text',
            ],
            [
                'key' => 'hero_description',
                'value' => 'Kelola tugas, catatan, dan jadwal kuliah dalam satu platform yang elegan dan efisien.',
                'type' => 'text',
            ],
            [
                'key' => 'footer_copyright',
                'value' => '© 2025 Notezque. All Rights Reserved.',
                'type' => 'text',
            ],
            [
                'key' => 'footer_subtitle',
                'value' => 'Platform Manajemen Tugas dan Produktivitas Akademik',
                'type' => 'text',
            ],
        ];

        foreach ($konten as $item) {
            KontenStatis::updateOrCreate(
                ['key' => $item['key']],
                [
                    'value' => $item['value'],
                    'type' => $item['type'],
                ]
            );
        }
    }
}
