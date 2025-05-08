<?php

namespace App\Helpers;

use App\Models\Minuman;

class DrinkPriceHelper
{
    /**
     * Hitung harga total berdasarkan minuman dan pilihan (bisa default atau custom).
     */
    public static function calculate(Minuman $minuman, $sizeId = null, $sugarId = null, $toppingId = null)
    {
        $total = $minuman->base_price;

        // Pakai default jika tidak ada input
        $size = $sizeId ? $minuman->sizes()->find($sizeId) : $minuman->defaultSize;
        $sugar = $sugarId ? $minuman->sugars()->find($sugarId) : $minuman->defaultSugar;
        $topping = $toppingId ? $minuman->toppings()->find($toppingId) : $minuman->defaultTopping;

        if ($size) {
            $total += optional($size)->price;
        }

        if ($sugar) {
            $total += optional($sugar)->price;
        }

        if ($topping) {
            $total += optional($topping)->price;
        }

        return $total;
    }
}
