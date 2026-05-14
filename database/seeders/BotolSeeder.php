<?php

namespace Database\Seeders;

use App\Models\Botolan;
use Illuminate\Database\Seeder;

class BotolSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama'              => 'Es Teh Manis',
                'deskripsi'         => 'Teh manis segar diseduh dari teh pilihan, dikemas dalam botol higienis. Cocok diminum dingin sebagai teman bersantai.',
                'short_description' => 'Teh manis segar dalam botol',
                'ukurans'           => [
                    ['label' => '250 ml', 'harga' => 8000],
                    ['label' => '500 ml', 'harga' => 13000],
                    ['label' => '1 Liter', 'harga' => 22000],
                ],
                'image_seed' => 'teh-manis',
            ],
            [
                'nama'              => 'Es Jeruk Peras',
                'deskripsi'         => 'Jeruk segar diperas langsung dan dikemas dalam botol. Kaya vitamin C, menyegarkan dan alami tanpa pengawet.',
                'short_description' => 'Jeruk peras segar tanpa pengawet',
                'ukurans'           => [
                    ['label' => '250 ml', 'harga' => 10000],
                    ['label' => '500 ml', 'harga' => 17000],
                    ['label' => '1 Liter', 'harga' => 30000],
                ],
                'image_seed' => 'jeruk-peras',
            ],
            [
                'nama'              => 'Jus Mangga',
                'deskripsi'         => 'Jus mangga harum manis dibuat dari buah mangga pilihan yang matang sempurna. Tekstur kental, rasa autentik buah asli.',
                'short_description' => 'Jus mangga kental dari buah segar',
                'ukurans'           => [
                    ['label' => '250 ml', 'harga' => 12000],
                    ['label' => '500 ml', 'harga' => 20000],
                    ['label' => '1 Liter', 'harga' => 35000],
                ],
                'image_seed' => 'jus-mangga',
            ],
            [
                'nama'              => 'Minuman Jahe Hangat',
                'deskripsi'         => 'Minuman jahe rempah pilihan yang menghangatkan tubuh. Cocok dinikmati pagi hari atau saat cuaca dingin.',
                'short_description' => 'Jahe rempah menghangatkan tubuh',
                'ukurans'           => [
                    ['label' => '250 ml', 'harga' => 9000],
                    ['label' => '500 ml', 'harga' => 15000],
                    ['label' => '1 Liter', 'harga' => 25000],
                ],
                'image_seed' => 'jahe-hangat',
            ],
            [
                'nama'              => 'Es Coklat Susu',
                'deskripsi'         => 'Perpaduan coklat premium dan susu segar yang creamy. Lembut di tenggorokan, cocok untuk semua usia.',
                'short_description' => 'Coklat susu creamy dalam botol',
                'ukurans'           => [
                    ['label' => '250 ml', 'harga' => 13000],
                    ['label' => '500 ml', 'harga' => 22000],
                    ['label' => '1 Liter', 'harga' => 38000],
                ],
                'image_seed' => 'coklat-susu',
            ],
        ];

        // Image seeds mapped to Picsum photo IDs for consistent results
        $picsum = [
            'teh-manis'   => 430,  // warm tea-like tones
            'jeruk-peras' => 442,  // orange/citrus tones
            'jus-mangga'  => 429,  // yellow/tropical tones
            'jahe-hangat' => 431,  // warm brown tones
            'coklat-susu' => 427,  // dark brown tones
        ];

        foreach ($data as $item) {
            $botolan = Botolan::create([
                'nama'              => $item['nama'],
                'deskripsi'         => $item['deskripsi'],
                'short_description' => $item['short_description'],
                'is_habis'          => false,
                'is_active'         => true,
            ]);

            foreach ($item['ukurans'] as $ukuran) {
                $botolan->allUkurans()->create([
                    'label'     => $ukuran['label'],
                    'harga'     => $ukuran['harga'],
                    'is_active' => true,
                ]);
            }

            $photoId = $picsum[$item['image_seed']];
            $url = "https://picsum.photos/id/{$photoId}/400/400";

            try {
                $botolan->addMediaFromUrl($url)
                    ->usingFileName($item['image_seed'] . '.jpg')
                    ->toMediaCollection('foto');
            } catch (\Exception $e) {
                $this->command->warn("Gagal mengunduh gambar untuk {$item['nama']}: " . $e->getMessage());
            }
        }

        $this->command->info('BotolSeeder selesai: 5 botolan ditambahkan.');
    }
}
