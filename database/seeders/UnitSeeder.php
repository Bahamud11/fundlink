<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'name' => 'SD Al-Hikmah Jakarta',
                'address' => 'Jakarta',
                'google_maps_url' => 'https://maps.google.com/?q=SD+Al-Hikmah+Jakarta',
            ],
            [
                'name' => 'SMP Darul Ulum Bogor',
                'address' => 'Bogor',
                'google_maps_url' => 'https://maps.google.com/?q=SMP+Darul+Ulum+Bogor',
            ],
            [
                'name' => 'SMA Harapan Bangsa Depok',
                'address' => 'Depok',
                'google_maps_url' => 'https://maps.google.com/?q=SMA+Harapan+Bangsa+Depok',
            ],
            [
                'name' => 'Panti Asuhan Kasih Ibu Bekasi',
                'address' => 'Bekasi',
                'google_maps_url' => 'https://maps.google.com/?q=Panti+Asuhan+Kasih+Ibu+Bekasi',
            ],
            [
                'name' => 'Pesantren Nurul Huda Tangerang',
                'address' => 'Tangerang',
                'google_maps_url' => 'https://maps.google.com/?q=Pesantren+Nurul+Huda+Tangerang',
            ],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
