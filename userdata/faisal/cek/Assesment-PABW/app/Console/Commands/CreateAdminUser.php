<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user {--email=} {--password=} {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name') ?? $this->ask('Enter name for the admin user');
        $email = $this->option('email') ?? $this->ask('Enter email for the admin user');
        $password = $this->option('password') ?? $this->secret('Enter password for the admin user');

        // Validasi input
        if (empty($name) || empty($email) || empty($password)) {
            $this->error('All fields are required: name, email, password');
            return 1;
        }

        // Cek apakah user sudah ada
        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists!");
            return 1;
        }

        // Buat user baru
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin', // Set role sebagai admin
            'coins' => 0,
        ]);

        $this->info("Admin user created successfully!");
        $this->info("Name: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Role: {$user->role}");

        return 0;
    }
}
