<?php

namespace Database\Seeders;

use App\Models\Role;
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
        // Ambil role yang sudah dibuat di RoleSeeder
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        // Admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'phone' => '081234567890',
            'password' => Hash::make('dsadsadsa'),
            'role_id' => $adminRole->id
        ]);

        // User Biasa
        User::factory()->create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'phone' => '081234567890',
            'password' => Hash::make('dsadsadsa'),
            'role_id' => $userRole->id
        ]);
    }
}