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
     */
    public function run(): void
    {
        $units = Unit::all();
        $users = User::where('role', 'user')->get();

        if ($units->isEmpty() || $users->isEmpty()) {
            return;
        }

        $categories = [
            'pemasukan' => ['Dana BOS', 'Donasi', 'Zakat', 'Infaq', 'Sewa Gedung', 'Lain-lain'],
            'pengeluaran' => ['Gaji Pegawai', 'Listrik & Air', 'Internet', 'Konsumsi', 'Alat Tulis Kantor', 'Pemeliharaan', 'Kegiatan Siswa']
        ];

        // Generate transactions for the last 14 days
        for ($i = 0; $i <= 14; $i++) {
            $date = Carbon::now()->subDays($i);
            
            // Create 3-5 transactions per day
            $count = rand(3, 5);
            
            for ($j = 0; $j < $count; $j++) {
                $type = rand(0, 1) ? 'pemasukan' : 'pengeluaran';
                $unit = $units->random();
                $user = $users->where('unit_id', $unit->id)->first() ?? $users->random();
                
                Transaction::create([
                    'unit_id' => $unit->id,
                    'user_id' => $user->id,
                    'type' => $type,
                    'amount' => $type === 'pemasukan' ? rand(1000000, 10000000) : rand(100000, 2000000),
                    'category' => $categories[$type][array_rand($categories[$type])],
                    'description' => "Transaksi otomatis hari ke-$i ke-$j",
                    'transaction_date' => $date,
                ]);
            }
        }
    }
}
