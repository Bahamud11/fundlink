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
                'name' => 'Sekolah Dasar Al-Hikmah',
                'address' => 'Jl. Raya Pendidikan No. 123, Jakarta',
                'google_maps_url' => 'https://maps.google.com/?q=SD+Al-Hikmah',
            ],
            [
                'name' => 'Pesantren Darul Ulum',
                'address' => 'Kecamatan Cerdas, Bogor',
                'google_maps_url' => 'https://maps.google.com/?q=Pesantren+Darul+Ulum',
            ],
            [
                'name' => 'Panti Asuhan Kasih Ibu',
                'address' => 'Kelurahan Harapan, Bandung',
                'google_maps_url' => 'https://maps.google.com/?q=Panti+Asuhan+Kasih+Ibu',
            ],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
