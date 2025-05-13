<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Minuman;

class MinumanDetail extends Component
{
    public $minuman;
    public $selectedSizeId;
    public $selectedSugarId;
    public $selectedToppingId;
    public $cart = []; 
    public $sizes = [];
    public $sugars = [];
    public $toppings = [];

    public function mount($id)
    {
        $this->minuman = Minuman::with(['sizes', 'sugars', 'toppings'])->findOrFail($id);
    
        $this->sizes = $this->minuman->sizes;
        $this->sugars = $this->minuman->sugars;
        $this->toppings = $this->minuman->toppings;
    
        $this->selectedSizeId = $this->minuman->default_size_id;
        $this->selectedSugarId = $this->minuman->default_sugar_id;
        $this->selectedToppingId = $this->minuman->default_topping_id;
    }
    // public function updated($property)
    // {
    //     if (in_array($property, ['selectedSizeId', 'selectedSugarId', 'selectedToppingId'])) {
    //         $this->dispatch('$refresh');
    //     }
    // }
    // Metode untuk menambahkan produk ke keranjang
    public function getTotalPriceProperty()
    {
        $size = collect($this->sizes)->firstWhere('id', $this->selectedSizeId);
        $sugar = collect($this->sugars)->firstWhere('id', $this->selectedSugarId);
        $topping = collect($this->toppings)->firstWhere('id', $this->selectedToppingId);
    
        return $this->minuman->base_price
            + ($size['price'] ?? 0)
            + ($sugar['price'] ?? 0)
            + ($topping['default_price'] ?? 0);
    }

    public function addToCart()
    {
        $key = $this->minuman->id . '-' . $this->selectedSizeId . '-' . $this->selectedSugarId . '-' . $this->selectedToppingId;

        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            $cart[$key]['qty']++;
        } else {
            $cart[$key] = [
                'id' => $this->minuman->id,
                'size_id' => $this->selectedSizeId,
                'sugar_id' => $this->selectedSugarId,
                'topping_id' => $this->selectedToppingId,
                'price' => $this->totalPrice,
                'qty' => 1,
            ];
        }

        session()->put('cart', $cart);
        
        // Menampilkan pesan sukses ke pengguna
        session()->flash('message', 'Produk berhasil ditambahkan ke keranjang!');

        // Mengembalikan response JSON dengan data cart terbaru
        // return response()->json([
        //     'message' => 'Produk berhasil ditambahkan ke keranjang',
        //     'cart' => $cart,
        // ]);
    }

    public function render()
    {
        return view('livewire.minuman-detail')->layout('layouts.public');
    }
}