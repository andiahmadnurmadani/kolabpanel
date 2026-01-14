<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat admin default jika belum ada
        User::firstOrCreate(
            ['email' => 'admin@notezque.org'],
            [
                'name' => 'Admin NotezQue',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]
        );
    }
}
