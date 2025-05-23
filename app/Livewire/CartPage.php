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
    public $nama_pemesan = '';
    public $alamat_pengantaran = '';
    public $waktu_pengantaran = '';
    public $catatan = '';
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
                'minuman_id' => $item['id'],
                'qty' => $item['qty'],
                'harga' => $item['price'],
                'subtotal' => $item['qty'] * $item['price'],
                'minuman' => $minuman?->nama ?? 'Unknown Drink',
                'size' => $size?->name ?? null,
                'gula' => $sugar?->level ?? null,
                'topping' => $topping?->nama ?? null,
                'catatan' => $item['catatan'] ?? null,
            ];
        });
    
        // Generate or get session ID
        $sessionId = session()->getId();
        
        // Store session ID in cookie for persistence
        \Illuminate\Support\Facades\Cookie::queue('user_session', $sessionId, 60 * 24 * 30); // 30 days
        
        // Create the order
        $pesanan = \App\Models\Pesanan::create([
            'user_id' => auth()->id(),
            'session_id' => $sessionId,
            'nama_pemesan' => $this->nama_pemesan,
            'alamat_pengantaran' => $this->alamat_pengantaran,
            'waktu_pengantaran' => $this->waktu_pengantaran,
            'catatan' => $this->catatan ?? null,
            'total' => (int) $this->total,
            'total_harga' => $this->total,
            'status' => 'menunggu_konfirmasi',
            'nomor_pesanan' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(uniqid())
        ]);
        
        // Create order details
        foreach ($cartItems as $item) {
            $pesanan->details()->create([
                'minuman_id' => $item['minuman_id'],
                'nama_minuman' => $item['minuman'],
                'harga' => $item['harga'],
                'qty' => $item['qty'],
                'size' => $item['size'],
                'gula' => $item['gula'],
                'topping' => $item['topping'],
                'catatan' => $item['catatan'],
                'subtotal' => $item['subtotal'],
            ]);
        }
    
        // Prepare WhatsApp message
        $message = "Assalamualaikum, saya ingin pesan:\n\n";
        foreach ($cartItems as $item) {
            $message .= "- {$item['minuman']}";
            $message .= $item['size'] ? " (Size: {$item['size']}" : "";
            $message .= $item['gula'] ? ", Gula: {$item['gula']}" : "";
            $message .= $item['topping'] ? ", Topping: {$item['topping']}" : "";
            $message .= $item['size'] ? ")" : "";
            $message .= " x {$item['qty']} = Rp " . number_format($item['subtotal'], 0, ',', '.') . "\n";
            if (!empty($item['catatan'])) {
                $message .= "  Catatan: {$item['catatan']}\n";
            }
        }
    
        $message .= "\nTotal: Rp " . number_format($this->total, 0, ',', '.');
        $message .= "\n\nNama: {$this->nama_pemesan}";
        $message .= "\nAlamat: {$this->alamat_pengantaran}";
        $message .= "\nWaktu Pengantaran: {$this->waktu_pengantaran}";
        $message .= "\nNomor Pesanan: " . $pesanan->nomor_pesanan;
    
        // Clear cart
        session()->forget('cart');
        $this->dispatch('cartUpdated');
        $this->dispatch('clearLocalCart');
        
        // Redirect to WhatsApp
        $wa = '6282375207570'; // Replace with your WhatsApp number
        return redirect()->away("https://wa.me/{$wa}?text=" . urlencode($message));
    }
}
