<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Generate diverse transactions:
     * - Covering 2 years back
     * - Max 2 transactions per day per unit
     * - Spread across weekly, monthly, and yearly ranges
     */
    public function run(): void
    {
        $units = Unit::all();

        if ($units->isEmpty()) {
            $this->command->warn('No units found. Please run UnitSeeder first.');
            return;
        }

        $categories = [
            'pemasukan' => [
                'Dana BOS',
                'Donasi',
                'Zakat',
                'Infaq',
                'Iuran Siswa',
                'Bantuan Pemerintah',
                'Hibah',
                'Pendapatan Usaha',
                'Lainnya',
            ],
            'pengeluaran' => [
                'Gaji Pegawai',
                'Listrik & Air',
                'Internet',
                'Pemeliharaan',
                'Alat Tulis Kantor',
                'Konsumsi',
                'Kegiatan Siswa',
                'Transportasi',
                'Kebersihan',
                'Perlengkapan',
                'Lainnya',
            ]
        ];

        $descriptions = [
            'pemasukan' => [
                'Pembayaran SPP bulan ini',
                'Sumbangan dari donatur tetap',
                'Dana operasional rutin',
                'Infaq Jum\'at',
                'Bantuan dari pemerintah daerah',
                'Donasi alumni',
                'Hasil kerjasama dengan pihak ketiga',
                'Pendapatan kegiatan bazar',
            ],
            'pengeluaran' => [
                'Pembayaran gaji bulanan staff',
                'Tagihan listrik dan air bulan ini',
                'Pembelian perlengkapan kebersihan',
                'Biaya internet dan telepon',
                'Pembelian alat tulis untuk siswa',
                'Konsumsi rapat bulanan',
                'Biaya pemeliharaan gedung',
                'Transportasi kegiatan ekstrakurikuler',
                'Pembelian buku pelajaran',
            ],
        ];

        // Generate for 2 years back (730 days)
        $startDate = Carbon::now()->subYears(2);
        $endDate = Carbon::now();

        $this->command->info('Generating transactions from ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'));

        $totalDays = $startDate->diffInDays($endDate);
        $transactionCount = 0;

        // Loop through each day
        for ($day = 0; $day <= $totalDays; $day++) {
            $currentDate = $startDate->copy()->addDays($day);

            // Skip some days randomly to make data more realistic (60% chance to have transactions)
            if (rand(1, 100) > 60) {
                continue;
            }

            // Each unit gets 0-2 transactions per day
            foreach ($units as $unit) {
                $transactionsPerDay = rand(0, 2);

                // Get users for this unit
                $unitUsers = User::where('unit_id', $unit->id)->get();
                if ($unitUsers->isEmpty()) {
                    continue;
                }

                for ($i = 0; $i < $transactionsPerDay; $i++) {
                    $type = rand(0, 100) > 40 ? 'pemasukan' : 'pengeluaran'; // 60% pemasukan, 40% pengeluaran
                    $category = $categories[$type][array_rand($categories[$type])];

                    // Amount varies by type and category
                    if ($type === 'pemasukan') {
                        $amount = match($category) {
                            'Dana BOS' => rand(50000000, 100000000),
                            'Bantuan Pemerintah' => rand(20000000, 50000000),
                            'Hibah' => rand(10000000, 30000000),
                            'Iuran Siswa' => rand(5000000, 15000000),
                            'Donasi' => rand(1000000, 10000000),
                            'Zakat', 'Infaq' => rand(500000, 5000000),
                            default => rand(500000, 3000000),
                        };
                    } else {
                        $amount = match($category) {
                            'Gaji Pegawai' => rand(30000000, 60000000),
                            'Pemeliharaan' => rand(5000000, 15000000),
                            'Kegiatan Siswa' => rand(2000000, 8000000),
                            'Listrik & Air' => rand(1000000, 3000000),
                            'Konsumsi' => rand(500000, 2000000),
                            'Internet' => rand(500000, 1500000),
                            default => rand(200000, 1000000),
                        };
                    }

                    Transaction::create([
                        'unit_id' => $unit->id,
                        'user_id' => $unitUsers->random()->id,
                        'type' => $type,
                        'amount' => $amount,
                        'category' => $category,
                        'description' => $descriptions[$type][array_rand($descriptions[$type])],
                        'transaction_date' => $currentDate,
                        'attachment_path' => null,
                    ]);

                    $transactionCount++;
                }
            }
        }

        $this->command->info("Successfully created {$transactionCount} transactions across {$units->count()} units over 2 years.");
    }
}
