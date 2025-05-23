<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Minuman;
use App\Models\Size;
use App\Models\Sugar;
use App\Models\Topping;
use App\Models\WebSetting;
class CartPage extends Component
{
    public $cart = [];
    public $showForm = false;
    public $nama_pemesan, $alamat_pengantaran, $waktu_pengantaran;
    public $total = 0;
    public function mount()
    {
        // Sinkronkan keranjang dari Local Storage ke Session saat komponen di-mount
        $this->syncCartFromLocalStorage();
        $this->updateTotal();
    }
    
    protected function syncCartFromLocalStorage()
    {
        // Sinkronkan dari Local Storage ke Session
        $this->cart = session()->get('cart', []);
    }
    public function updateTotal()
    {
        $this->total = collect($this->cart)->sum(function ($item) {
            return $item['qty'] * $item['price'];
        });
    }
    public function increaseQty($key)
    {
        $this->cart[$key]['qty']++;
        session()->put('cart', $this->cart);
        $this->updateTotal();
        $this->dispatch('cartUpdated');
        
        // Update Local Storage
        $this->dispatch('updateLocalCart', cart: $this->cart);
    }

    public function decreaseQty($key)
    {
        if ($this->cart[$key]['qty'] > 1) {
            $this->cart[$key]['qty']--;
            session()->put('cart', $this->cart);
            $this->updateTotal();
            $this->dispatch('cartUpdated');
            
            // Update Local Storage
            $this->dispatch('updateLocalCart', cart: $this->cart);
        }
    }

    public function removeItem($key)
    {
        unset($this->cart[$key]);
        $this->cart = array_values($this->cart); // Reset array keys
        session()->put('cart', $this->cart);
        $this->updateTotal();
        $this->dispatch('cartUpdated');
        
        // Update Local Storage
        $this->dispatch('updateLocalCart', cart: $this->cart);
    }

    public function clearCart()
    {
        $this->cart = [];
        session()->forget('cart');
        $this->updateTotal();
        $this->dispatch('cartUpdated');
        
        // Clear Local Storage
        $this->dispatch('clearLocalCart');
    }
    public function checkout()
    {
        $this->showForm = true;
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
                'size' => $size?->name ?? '-',
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
    public function konfirmasiCheckout()
    {
        $this->validate([
            'nama_pemesan' => 'required',
            'alamat_pengantaran' => 'required',
            'waktu_pengantaran' => 'required',
        ]);
    
        $cartItems = collect($this->cart)->map(function ($item, $key) {
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
                'size' => $size?->name ?? '-',
                'sugar' => $sugar?->level ?? '-',
                'topping' => $topping?->nama ?? '-',
            ];
        });
    
        $pesanan = \App\Models\Pesanan::create([
            'nama_pemesan' => $this->nama_pemesan,
            'alamat_pengantaran' => $this->alamat_pengantaran,
            'waktu_pengantaran' => $this->waktu_pengantaran,
            'items' => json_encode($this->cart),
            'total' => $this->total,
        ]);
    
        $message = "Assalamualaikum, saya ingin pesan:\n\n";
        foreach ($cartItems as $item) {
            $message .= "- {$item['minuman']} (Size: {$item['size']}, Gula: {$item['sugar']}, Topping: {$item['topping']}) x {$item['qty']} = Rp " . number_format($item['subtotal'], 0, ',', '.') . "\n";
        }
    
        $message .= "\nTotal: Rp " . number_format($this->total, 0, ',', '.');
        $message .= "\n\nNama: {$this->nama_pemesan}";
        $message .= "\nAlamat: {$this->alamat_pengantaran}";
        $message .= "\nWaktu Pengantaran: {$this->waktu_pengantaran}";
        $message .= "\nKode Pesanan: #" . $pesanan->id;
    
        $wa = '6282375207570'; // Ganti dengan nomor Anda
        session()->forget('cart');
        return redirect()->away("https://wa.me/{$wa}?text=" . urlencode($message));

    }
}
