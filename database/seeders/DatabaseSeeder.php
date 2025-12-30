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
        // Default Site Settings
        $this->call(SystemSettingsSeeder::class);

        // Create Default Admin
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@cloudhost.com',
            'password' => bcrypt('admin123'), // User should change this immediately
            'is_admin' => true,
            'security_question' => 'What is my secret?',
            'security_answer' => 'secret',
            'balance' => 0,
        ]);
        
        // Optional: Create a test user
        User::create([
            'name' => 'Test User',
            'username' => 'user123',
            'email' => 'user@example.com',
            'password' => bcrypt('user123'),
            'is_admin' => false,
            'security_question' => 'What is my secret?',
            'security_answer' => 'secret',
            'balance' => 0,
        ]);
    }
}
