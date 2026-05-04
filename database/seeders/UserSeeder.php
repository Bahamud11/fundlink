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
        // Admin
        User::create([
            'name' => 'Admin Pusat',
            'email' => 'admin@fundlink.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Unit Users
        $users = [
            [
                'name' => 'Budi - SD Al-Hikmah',
                'email' => 'budi@fundlink.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => 1,
            ],
            [
                'name' => 'Siti - Pesantren Darul Ulum',
                'email' => 'siti@fundlink.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => 2,
            ],
            [
                'name' => 'Andi - Panti Asuhan',
                'email' => 'andi@fundlink.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => 3,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
