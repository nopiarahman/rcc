<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ThemeColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $themes = [
            [
                'name' => 'green',
                'display_name' => 'Hijau',
                'primary_color' => '#011a0f',
                'secondary_color' => '#006a3e',
                'button_bg_color' => '#006a3e',
                'button_text_color' => '#ffffff',
                'card_bg_color' => '#ffffff',
                'text_color' => '#1a1a1a',
                'muted_text_color' => '#6b7280',
                'is_active' => true,
            ],
            [
                'name' => 'brown',
                'display_name' => 'Coklat',
                'primary_color' => '#3e2723',
                'secondary_color' => '#8d6e63',
                'button_bg_color' => '#5d4037',
                'button_text_color' => '#ffffff',
                'card_bg_color' => '#ffffff',
                'text_color' => '#1a1a1a',
                'muted_text_color' => '#6b7280',
                'is_active' => true,
            ],
            [
                'name' => 'yellow',
                'display_name' => 'Kuning',
                'primary_color' => '#ff6f00',
                'secondary_color' => '#ffc107',
                'button_bg_color' => '#ff8f00',
                'button_text_color' => '#1a1a1a',
                'card_bg_color' => '#ffffff',
                'text_color' => '#1a1a1a',
                'muted_text_color' => '#6b7280',
                'is_active' => true,
            ],
            [
                'name' => 'white_green',
                'display_name' => 'Putih Hijau',
                'primary_color' => '#f5f9f0',
                'secondary_color' => '#4a5d4a',
                'button_bg_color' => '#4a5d4a',
                'button_text_color' => '#ffffff',
                'card_bg_color' => '#ffffff',
                'text_color' => '#2d2d2d',
                'muted_text_color' => '#6b7280',
                'is_active' => true,
            ],
            [
                'name' => 'dark_green',
                'display_name' => 'Hijau Gelap',
                'primary_color' => '#2c3e2c',
                'secondary_color' => '#3d4f3d',
                'button_bg_color' => '#3d4f3d',
                'button_text_color' => '#ffffff',
                'card_bg_color' => '#f8f9fa',
                'text_color' => '#2d2d2d',
                'muted_text_color' => '#6b7280',
                'is_active' => true,
            ],
            [
                'name' => 'blue',
                'display_name' => 'Biru',
                'primary_color' => '#0d47a1',
                'secondary_color' => '#2196f3',
                'button_bg_color' => '#1565c0',
                'button_text_color' => '#ffffff',
                'card_bg_color' => '#ffffff',
                'text_color' => '#1a1a1a',
                'muted_text_color' => '#6b7280',
                'is_active' => true,
            ],
            [
                'name' => 'orange',
                'display_name' => 'Oranye',
                'primary_color' => '#e65100',
                'secondary_color' => '#ff9800',
                'button_bg_color' => '#ef6c00',
                'button_text_color' => '#ffffff',
                'card_bg_color' => '#ffffff',
                'text_color' => '#1a1a1a',
                'muted_text_color' => '#6b7280',
                'is_active' => true,
            ],
        ];

        foreach ($themes as $theme) {
            DB::table('theme_colors')->updateOrInsert(
                ['name' => $theme['name']],
                $theme
            );
        }
    }
}
