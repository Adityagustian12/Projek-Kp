<?php

namespace Database\Seeders;

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
        // Create single Admin User only
        User::create([
            'name' => 'Admin Kos-Kosan',
            'email' => 'admin@koskosan.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'address' => 'Jl. Admin No. 1, Jakarta',
            'role' => 'admin',
        ]);
    }
}