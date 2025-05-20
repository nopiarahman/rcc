<?php

namespace Database\Seeders;

use App\Models\WebSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WebSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update web settings
        WebSetting::firstOrCreate(
            ['id' => 1],
            [
                'site_name' => 'Raihaan Coffee Corner',
                'logo_path' => null,
                'favicon_path' => null,
            ]
        );
    }
}
