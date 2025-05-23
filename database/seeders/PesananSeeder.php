<?php

namespace Database\Seeders;

use App\Models\Pesanan;
use App\Models\Minuman;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create test minuman
        $minuman = Minuman::firstOrCreate(
            ['nama' => 'Es Teh Manis'],
            [
                'base_price' => 5000, 
                'deskripsi' => 'Es teh manis segar',
                'short_description' => 'Es teh manis segar'
            ]
        );

        // Create test orders with different statuses
        $statuses = ['menunggu_konfirmasi', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
        $now = now();

        foreach ($statuses as $index => $status) {
            $orderDate = $now->copy()->subDays(count($statuses) - $index);
            
            $pesanan = Pesanan::create([
                'nomor_pesanan' => 'ORD-' . $orderDate->format('Ymd') . strtoupper(uniqid()),
                'nama_pemesan' => 'Pelanggan Test ' . ($index + 1),
                'nomor_telepon' => '0812345678' . $index,
                'alamat_pengantaran' => 'Jl. Contoh No.' . ($index + 1) . ', Kota Test',
                'waktu_pengantaran' => $orderDate->copy()->addHours(2)->format('Y-m-d H:i:s'),
                'catatan' => 'Ini adalah pesanan test dengan status ' . $status,
                'total' => 15000 + ($index * 5000),
                'total_harga' => 15000 + ($index * 5000),
                'status' => $status,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Add order details
            $pesanan->details()->create([
                'minuman_id' => $minuman->id,
                'nama_minuman' => $minuman->nama,
                'harga' => $minuman->base_price,
                'qty' => $index + 1,
                'size' => $index % 2 ? 'Medium' : 'Large',
                'gula' => ['Sedang', 'Sedikit', 'Banyak'][$index % 3],
                'topping' => $index % 2 ? 'Boba' : 'Jelly',
                'catatan' => 'Catatan untuk item ' . ($index + 1),
                'subtotal' => $minuman->base_price * ($index + 1),
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Add a second item for some orders
            if ($index % 2 === 0) {
                $pesanan->details()->create([
                    'minuman_id' => $minuman->id,
                    'nama_minuman' => $minuman->nama . ' Extra',
                    'harga' => $minuman->base_price + 2000,
                    'qty' => 1,
                    'size' => 'Large',
                    'gula' => 'Sedang',
                    'topping' => 'Cincau',
                    'catatan' => 'Tambahan es batu',
                    'subtotal' => $minuman->base_price + 2000,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);
            }
        }
    }
}
