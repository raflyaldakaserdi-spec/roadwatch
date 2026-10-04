<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Akun Admin Bawaan
        User::create([
            'name' => 'Rafly Aldakas Erdi',
            'email' => 'raflyaldakaserdi@gmail.com',
            'password' => Hash::make('password123'), // Password bawaan
            'role' => 'admin',
        ]);

        // Jalankan Seeder Detection Data
        $this->call([
            DetectionSeeder::class,
        ]);
    }
}