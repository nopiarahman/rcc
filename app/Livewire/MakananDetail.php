<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Makanan;

class MakananDetail extends Component
{
    public $makanan;
    public $selectedToppingId;
    public $toppings = [];
    public $theme;

    public function mount($makanan)
    {
        $this->makanan = \App\Models\Makanan::with(['toppings', 'bahans'])->findOrFail($makanan);
        $this->toppings = $this->makanan->toppings;
        $this->selectedToppingId = $this->makanan->default_topping ?? ($this->toppings->first()->id ?? null);
        $webSetting = \App\Models\WebSetting::first();
        $this->theme = $webSetting ? $webSetting->theme : 'green';
    }

    public function getTotalPriceProperty()
    {
        $topping = collect($this->toppings)->firstWhere('id', $this->selectedToppingId);
        $totalPrice = $this->makanan->base_price
            + ($topping['default_price'] ?? 0);
        // Diskon jika ada
        if ($activeDiscount = $this->makanan->activeDiscount()) {
            return $activeDiscount->calculateDiscountedPrice($totalPrice);
        }
        return $totalPrice;
    }

    public function addToCart()
    {
        if ($this->makanan->is_habis) {
            session()->flash('message', 'Maaf, makanan ini sedang habis!');
            return;
        }
        
        $key = 'makanan-' . $this->makanan->id . '-' . $this->selectedToppingId;
        $cart = session()->get('cart', []);
        
        if (isset($cart[$key])) {
            $cart[$key]['qty']++;
        } else {
            $cart[$key] = [
                'id' => $this->makanan->id,
                'type' => 'makanan', // Add type to identify as food
                'name' => $this->makanan->nama,
                'topping_id' => $this->selectedToppingId,
                'price' => $this->totalPrice,
                'qty' => 1,
                'model' => $this->makanan, // Store the full model
            ];
        }
        
        session()->put('cart', $cart);
        $this->dispatch('cartUpdated');
        session()->flash('message', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function render()
    {
        $webSettings = \App\Models\WebSetting::first();
        return view('livewire.makanan-detail', [
            'web_settings' => $webSettings
        ])->layout('layouts.public');
    }
}

