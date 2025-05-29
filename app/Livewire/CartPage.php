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
    public $order_type = 'delivery';
    public $web_settings;

    public function mount()
    {
        // Sinkronkan keranjang dari Local Storage ke Session saat komponen di-mount
        $this->syncCartFromLocalStorage();
        $this->updateTotal();
        
        // Load web settings
        $this->web_settings = WebSetting::first();
        
        // Set default order type based on web settings
        if ($this->web_settings->order_mode != 'both') {
            $this->order_type = $this->web_settings->order_mode;
        }
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
        // If order mode is 'both', show order type selection modal first
        if ($this->web_settings->order_mode === 'both') {
            $this->dispatch('show-order-type-modal');
        } else {
            // If order mode is specific (delivery or takeaway), proceed directly to checkout form
            $this->order_type = $this->web_settings->order_mode;
            $this->proceedToCheckout();
        }
    }
    
    protected function proceedToCheckout()
    {
        // For delivery orders, we need to check location first
        if ($this->order_type === 'delivery') {
            $this->dispatch('check-location');
            // The actual checkout will be triggered by a location-check-passed event
        } else {
            // For takeaway, no location check needed
            $this->showCheckoutForm();
        }
    }
    
    public function locationCheckPassed()
    {
        // This will be called by the JavaScript when location check passes
        $this->showCheckoutForm();
    }
    
    public function setOrderType($type)
    {
        $this->order_type = $type;
        $this->proceedToCheckout();
    }
    
    public function showCheckoutForm()
    {
        $this->showForm = true;
        $this->dispatch('show-checkout-modal');
    }
    public function render()
    {
        $detailedCart = collect($this->cart)->map(function ($item, $key) {
            $minuman = Minuman::find($item['id']);
            $size = $item['size_id'] ? Size::find($item['size_id']) : null;
            $sugar = $item['sugar_id'] ? Sugar::find($item['sugar_id']) : null;
            $topping = $item['topping_id'] ? Topping::find($item['topping_id']) : null;
            
            // Calculate regular price (without discount)
            $regularPrice = null;
            $discountInfo = null;
            
            if ($minuman && $minuman->activeDiscount()) {
                $discount = $minuman->activeDiscount();
                
                // Get the base price for this specific configuration
                $basePrice = $minuman->base_price;
                if ($size) $basePrice += $size->price;
                if ($sugar) $basePrice += $sugar->price;
                if ($topping) $basePrice += $topping->default_price;
                
                // Calculate regular price
                $regularPrice = $basePrice;
                
                // Get discount information
                $discountInfo = [
                    'name' => $discount->name,
                    'type' => $discount->discount_type,
                    'amount' => $discount->discount_amount,
                    'discount_text' => $discount->discount_type === 'percentage' 
                        ? $discount->discount_amount . '%' 
                        : 'Rp' . number_format($discount->discount_amount, 0, ',', '.'),
                ];
            }
    
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
                'regular_price' => $regularPrice,
                'discount_info' => $discountInfo,
                'has_discount' => $discountInfo !== null,
            ];
        });
    
        $total = $detailedCart->sum('subtotal');
        
        // Get web settings for order mode
        $webSettings = $this->web_settings ?: WebSetting::first();
    
        return view('livewire.cart-page', [
            'cartItems' => $detailedCart,
            'total' => $total,
            'orderMode' => $webSettings->order_mode,
            'web_settings' => $webSettings
        ])->layout('layouts.public');
    }
    public function konfirmasiCheckout()
    {
        $validationRules = [
            'nama_pemesan' => 'required',
            'waktu_pengantaran' => 'required',
        ];
        
        // Only require address for delivery orders
        if ($this->order_type === 'delivery') {
            $validationRules['alamat_pengantaran'] = 'required';
        }
        
        $this->validate($validationRules);
    
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
            'alamat_pengantaran' => $this->order_type === 'delivery' ? $this->alamat_pengantaran : 'Takeaway',
            'waktu_pengantaran' => $this->waktu_pengantaran,
            'catatan' => $this->catatan ?? null,
            'total' => (int) $this->total,
            'total_harga' => $this->total,
            'status' => 'menunggu_konfirmasi',
            'order_type' => $this->order_type,
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
        $message .= "\nJenis Pesanan: " . ($this->order_type === 'delivery' ? 'Antar ke Alamat' : 'Ambil Sendiri');
        if ($this->order_type === 'delivery') {
            $message .= "\nAlamat: {$this->alamat_pengantaran}";
        }
        $message .= "\nWaktu " . ($this->order_type === 'delivery' ? 'Pengantaran' : 'Pengambilan') . ": {$this->waktu_pengantaran}";
        $message .= "\nNomor Pesanan: " . $pesanan->nomor_pesanan;
    
        // Clear cart
        session()->forget('cart');
        $this->dispatch('cartUpdated');
        $this->dispatch('clearLocalCart');
        
        // Redirect to WhatsApp
        $wa = WebSetting::first()->whatsapp_number;
        return redirect()->away("https://wa.me/{$wa}?text=" . urlencode($message));
    }
}
