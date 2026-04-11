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
        // Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@utbk.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'school' => 'System Admin',
            'is_admin' => true,
        ]);

        // Regular User
        User::create([
            'name' => 'Budi Utomo',
            'email' => 'budi@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'school' => 'SMA Negeri 1 Jakarta',
            'is_admin' => false,
        ]);

        $this->call(ExamSeeder::class);
    }
}
