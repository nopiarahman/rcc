<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Makanan;
use App\Services\CartService;

class MakananDetail extends Component
{
    public $makanan;
    public $selectedToppingId;
    public $theme;

    public function mount($makanan)
    {
        $this->makanan = \App\Models\Makanan::with(['toppings', 'bahans'])->findOrFail($makanan);
        $toppings = $this->makanan->toppings;
        $this->selectedToppingId = $toppings->contains('id', $this->makanan->default_topping)
            ? $this->makanan->default_topping
            : $toppings->first()?->id;
        $webSetting = \App\Models\WebSetting::current();
        $this->theme = $webSetting ? $webSetting->theme : 'green';
    }

    public function getTotalPriceProperty()
    {
        $topping = $this->makanan->toppings->firstWhere('id', $this->selectedToppingId);
        $totalPrice = $this->makanan->base_price
            + ($topping?->default_price ?? 0);
        // Diskon jika ada
        if ($activeDiscount = $this->makanan->activeDiscount()) {
            return $activeDiscount->calculateDiscountedPrice($totalPrice);
        }
        return $totalPrice;
    }

    public function addToCart()
    {
        if ($this->makanan->fresh()?->is_habis ?? true) {
            session()->flash('message', 'Maaf, makanan ini sedang habis!');
            return;
        }

        if ($this->selectedToppingId && ! $this->makanan->toppings->contains('id', $this->selectedToppingId)) {
            session()->flash('message', 'Pilihan topping tidak tersedia untuk makanan ini.');
            return;
        }

        // Keranjang hanya menyimpan ID & pilihan; harga selalu dihitung ulang di server
        $cart = CartService::add(session()->get('cart', []), [
            'type' => 'makanan',
            'id' => $this->makanan->id,
            'topping_id' => $this->selectedToppingId ?: null,
            'qty' => 1,
        ]);

        session()->put('cart', $cart);
        $this->dispatch('cartUpdated');
        session()->flash('message', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function render()
    {
        $webSettings = \App\Models\WebSetting::current();
        return view('livewire.makanan-detail', [
            'web_settings' => $webSettings,
            'toppings' => $this->makanan->toppings,
        ])->layout('layouts.public')->title($this->makanan->nama);
    }
}
