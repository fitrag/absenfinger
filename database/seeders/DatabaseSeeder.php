<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat akun admin
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'username' => 'admin',
            'password' => 'password', // akan di-hash otomatis karena cast 'hashed'
            'level' => 'admin',
            'is_active' => true,
        ]);
    }
}
