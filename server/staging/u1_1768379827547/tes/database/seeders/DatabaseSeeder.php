<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Note;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed konten statis terlebih dahulu
        $this->call([
            KontenStatisSeeder::class,
        ]);

        // Create default user
        User::create([
            'name' => 'User NotezQu',
            'email' => 'user@notezque.test',
            'password' => Hash::make('password123'),
        ]);

        // Seed data dengan seeder terpisah
        $this->call([
            NoteSeeder::class,
            TaskSeeder::class,
            ActivitySeeder::class,
            AdminSeeder::class,
            FolderSeeder::class,
            FileSeeder::class,
        ]);
    }
}
