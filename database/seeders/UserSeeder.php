<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@portalberita.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Additional admin users
        User::create([
            'name' => 'Editor Berita',
            'email' => 'editor@portalberita.com',
            'password' => Hash::make('editor123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Regular users (for comments)
        User::create([
            'name' => 'Sucipto',
            'email' => 'sucipto@example.com',
            'password' => Hash::make('sucipto123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Kipli',
            'email' => 'Kipli@example.com',
            'password' => Hash::make('kipli123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Santoso',
            'email' => 'Santoso@example.com',
            'password' => Hash::make('santoso123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Users seeded successfully!');
    }
}
