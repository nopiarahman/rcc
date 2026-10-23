<?php

namespace App\Helpers;

use App\Models\Minuman;

class DrinkPriceHelper
{
    /**
     * Hitung harga total berdasarkan minuman dan pilihan (bisa default atau custom).
     * Dipakai untuk harga di menu, halaman detail, keranjang, dan checkout agar selalu sama.
     */
    public static function calculate(Minuman $minuman, $sizeId = null, $sugarId = null, $toppingId = null)
    {
        $options = static::resolveOptions($minuman, $sizeId, $sugarId, $toppingId);

        return $minuman->base_price
            + ($options['size']?->price ?? 0)
            + ($options['sugar']?->price ?? 0)
            + ($options['topping']?->default_price ?? 0);
    }

    /**
     * Pilihan yang berlaku untuk minuman ini. Hanya opsi yang terpasang di minuman yang dihitung;
     * jika tidak memilih, dipakai default-nya (selama default itu juga terpasang).
     *
     * @return array{size: ?\App\Models\Size, sugar: ?\App\Models\Sugar, topping: ?\App\Models\Topping, valid: bool}
     */
    public static function resolveOptions(Minuman $minuman, $sizeId = null, $sugarId = null, $toppingId = null): array
    {
        $size = $minuman->sizes->firstWhere('id', $sizeId ?? $minuman->default_size_id);
        $sugar = $minuman->sugars->firstWhere('id', $sugarId ?? $minuman->default_sugar_id);
        $topping = $minuman->toppings->firstWhere('id', $toppingId ?? $minuman->default_topping_id);

        // Pilihan eksplisit yang tidak terpasang di minuman ini dianggap tidak valid
        $valid = ($sizeId === null || $size !== null)
            && ($sugarId === null || $sugar !== null)
            && ($toppingId === null || $topping !== null);

        return compact('size', 'sugar', 'topping', 'valid');
    }
}
