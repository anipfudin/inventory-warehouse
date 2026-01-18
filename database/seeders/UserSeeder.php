<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@inventory.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Regular user
        User::create([
            'name' => 'Regular User',
            'email' => 'user@inventory.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // Additional users
        User::factory(3)->create(['role' => 'user']);
    }
}
