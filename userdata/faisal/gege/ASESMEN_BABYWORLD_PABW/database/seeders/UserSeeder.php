<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; 
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
   
        User::create([
            'name'              => 'Nadine Nathania',
            'email'             => 'thaniandnn@gmail.com',
            'password'          => '12345', 
            'address'           => 'Alamat contoh untuk Nadine',
            'role'              => 'customer',
            'security_question' => 'Apa makanan favoritmu?',
            'security_answer'   => 'bakso',
        ]);

        User::create([
            'name'              => 'Admin Utama',
            'email'             => 'admin@example.com',
            'password'          => 'admin123', 
            'address'           => 'Office address',
            'role'              => 'admin',
            'security_question' => 'Apa warna favoritmu?',
            'security_answer'   => 'biru',
        ]);
    }
}
