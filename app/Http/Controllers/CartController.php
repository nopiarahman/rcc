<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Login;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $data = $request->all();
    
        // Ambil nilai, fallback ke 0 jika tidak ada
        $basePrice = (int) ($data['base_price'] ?? 0);
        $sizeId = $data['size_id'] ?? null;
        $sugarId = $data['sugar_id'] ?? null;
        $toppingId = $data['topping_id'] ?? null;
    
        $sizePrice = (int) ($data['size_price'] ?? 0);
        $sugarPrice = (int) ($data['sugar_price'] ?? 0);
        $toppingPrice = (int) ($data['topping_price'] ?? 0);
    
        $totalPrice = $basePrice + $sizePrice + $sugarPrice + $toppingPrice;
    
        // Buat unique key tetap konsisten meski ada yang null
        $key = $data['drink_id'] . '-' . ($sizeId ?? '0') . '-' . ($sugarId ?? '0') . '-' . ($toppingId ?? '0');
    
        $cart = session()->get('cart', []);
    
        if (isset($cart[$key])) {
            $cart[$key]['qty']++;
        } else {
            $cart[$key] = [
                'drink_id' => $data['drink_id'],
                'size_id' => $sizeId,
                'sugar_id' => $sugarId,
                'topping_id' => $toppingId,
                'price' => $totalPrice,
                'qty' => 1,
            ];
        }
    
        session()->put('cart', $cart);
        
        // Dispatch event for Livewire components to update
        event(new Login('', new \Illuminate\Foundation\Auth\User(), false));
        
        return response()->json(['message' => 'Added to cart', 'cart' => $cart]);
    }
    
}
