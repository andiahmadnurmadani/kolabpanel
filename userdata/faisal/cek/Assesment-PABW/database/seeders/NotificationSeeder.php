<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // BUAT 10 DATA OTOMATIS
       Notification::factory()->count(10)->create();

    }
}
