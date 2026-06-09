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
            'name' => 'Admin Pusat Yayasan',
            'email' => 'admin@fundlink.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'unit_id' => null,
        ]);

        // 2 Users per Unit (5 units = 10 users)
        $usersData = [
            // Unit 1: SD Al-Hikmah Jakarta
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@fundlink.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => 1,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@fundlink.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => 1,
            ],

            // Unit 2: SMP Darul Ulum Bogor
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad@fundlink.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => 2,
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@fundlink.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => 2,
            ],

            // Unit 3: SMA Harapan Bangsa Depok
            [
                'name' => 'Rudi Hermawan',
                'email' => 'rudi@fundlink.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => 3,
            ],
            [
                'name' => 'Maya Sari',
                'email' => 'maya@fundlink.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => 3,
            ],

            // Unit 4: Panti Asuhan Kasih Ibu Bekasi
            [
                'name' => 'Joko Widodo',
                'email' => 'joko@fundlink.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => 4,
            ],
            [
                'name' => 'Rina Susanti',
                'email' => 'rina@fundlink.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => 4,
            ],

            // Unit 5: Pesantren Nurul Huda Tangerang
            [
                'name' => 'Hasan Basri',
                'email' => 'hasan@fundlink.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => 5,
            ],
            [
                'name' => 'Fatimah Zahra',
                'email' => 'fatimah@fundlink.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => 5,
            ],
        ];

        foreach ($usersData as $user) {
            User::create($user);
        }
    }
}
