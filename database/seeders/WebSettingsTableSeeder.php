<?php

namespace Database\Seeders;

use App\Models\WebSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WebSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the default theme
        $defaultTheme = DB::table('theme_colors')->where('name', 'green')->first();
        
        if (!$defaultTheme) {
            // If no theme exists, create a default one
            $defaultThemeId = DB::table('theme_colors')->insertGetId([
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
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('web_settings')->updateOrInsert(
            ['id' => 1],
            [
                'site_name' => 'Raihaan Coffee Corner',
                'tagline' => 'Kopi Nikmat, Hati Senang',
                'theme' => 'green',
                'latitude' => -1.66651,
                'longitude' => 103.65238,
                'delivery_radius' => 600,
                'order_mode' => 'dine_in',
                'whatsapp_number' => '6281234567890',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
