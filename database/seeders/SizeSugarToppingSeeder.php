<?php

namespace Database\Seeders;

use App\Models\Size;
use App\Models\Sugar;
use App\Models\Topping;
use Illuminate\Database\Seeder;

class SizeSugarToppingSeeder extends Seeder
{
    public function run()
    {
        // Sizes (using 'name' and 'price' columns)
        $sizes = [
            ['name' => 'Small', 'price' => 0],
            ['name' => 'Medium', 'price' => 3000],
            ['name' => 'Large', 'price' => 5000],
        ];

        foreach ($sizes as $size) {
            Size::updateOrCreate(
                ['name' => $size['name']],
                ['price' => $size['price']]
            );
        }

        // Sugars (using 'level' and 'price' columns)
        $sugars = [
            ['level' => 'Tanpa Gula', 'price' => 0],
            ['level' => 'Normal', 'price' => 1000],
            ['level' => 'Stevia', 'price' => 1000],
        ];

        foreach ($sugars as $sugar) {
            Sugar::updateOrCreate(
                ['level' => $sugar['level']],
                ['price' => $sugar['price']]
            );
        }

        // Toppings (using 'nama' and 'default_price' columns)
        $toppings = [
            ['nama' => 'Tanpa Topping', 'default_price' => 0],
            ['nama' => 'Cream Cheese', 'default_price' => 2000],
        ];

        foreach ($toppings as $topping) {
            Topping::updateOrCreate(
                ['nama' => $topping['nama']],
                ['default_price' => $topping['default_price']]
            );
        }
    }
}
