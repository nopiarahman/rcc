<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Minuman;
use App\Models\Size;
use App\Models\Sugar;
use App\Models\Topping;
class CartPage extends Component
{
    public $cart = [];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function increaseQty($key)
    {
        $this->cart[$key]['qty']++;
        session()->put('cart', $this->cart);
    }

    public function decreaseQty($key)
    {
        if ($this->cart[$key]['qty'] > 1) {
            $this->cart[$key]['qty']--;
            session()->put('cart', $this->cart);
        }
    }

    public function removeItem($key)
    {
        unset($this->cart[$key]);
        session()->put('cart', $this->cart);
    }

    public function clearCart()
    {
        $this->cart = [];
        session()->forget('cart');
    }

    public function render()
    {
        $detailedCart = collect($this->cart)->map(function ($item, $key) {
            $minuman = Minuman::find($item['id']);
            $size = $item['size_id'] ? Size::find($item['size_id']) : null;
            $sugar = $item['sugar_id'] ? Sugar::find($item['sugar_id']) : null;
            $topping = $item['topping_id'] ? Topping::find($item['topping_id']) : null;
    
            return [
                'key' => $key,
                'qty' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $item['qty'] * $item['price'],
                'minuman' => $minuman?->nama ?? 'Unknown Drink',
                'minuman_model' => $minuman,
                'size' => $size?->nama ?? '-',
                'sugar' => $sugar?->level ?? '-',
                'topping' => $topping?->nama ?? '-',
            ];
        });
    
        $total = $detailedCart->sum('subtotal');
    
        return view('livewire.cart-page', [
            'cartItems' => $detailedCart,
            'total' => $total
        ])->layout('layouts.public');
    }
}
