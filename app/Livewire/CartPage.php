<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Minuman;
use App\Models\Makanan;
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
        $originalTotal = collect($this->cart)->sum(function ($item) {
            return $item['qty'] * $item['price'];
        });
        // Round down to nearest 1000
        $this->total = floor($originalTotal / 1000) * 1000;
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
            $type = $item['type'] ?? 'minuman'; // Default to 'minuman' for backward compatibility
            $model = null;
            $size = null;
            $sugar = null;
            $topping = null;
            
            if ($type === 'minuman') {
                $model = Minuman::find($item['id']);
                // Safely access array keys with null coalescing operator
                $sizeId = $item['size_id'] ?? null;
                $sugarId = $item['sugar_id'] ?? null;
                $toppingId = $item['topping_id'] ?? null;
                
                $size = $sizeId ? Size::find($sizeId) : null;
                $sugar = $sugarId ? Sugar::find($sugarId) : null;
                $topping = $toppingId ? Topping::find($toppingId) : null;
                $name = $model?->nama ?? 'Unknown Drink';
            } else {
                $model = Makanan::find($item['id']);
                $toppingId = $item['topping_id'] ?? null;
                $topping = $toppingId ? Topping::find($toppingId) : null;
                $name = $model?->nama ?? 'Unknown Food';
            }
            
            // Calculate regular price (without discount)
            $regularPrice = null;
            $discountInfo = null;
            
            if ($model && $model->activeDiscount()) {
                $discount = $model->activeDiscount();
                
                // Get the base price for this specific configuration
                $basePrice = $model->harga;
                
                if ($type === 'minuman') {
                    if ($size) $basePrice += $size->price;
                    if ($sugar) $basePrice += $sugar->price;
                }
                
                if ($topping) {
                    $basePrice += $topping->default_price;
                }
                
                // Calculate regular price
                $regularPrice = $basePrice;
                
                // Get discount information
                $discountInfo = [
                    'name' => $discount->name,
                    'type' => $discount->discount_type,
                    'amount' => $discount->discount_amount,
                    'discount_text' => $discount->discount_type === 'percentage' 
                        ? (int)$discount->discount_amount . '%' 
                        : 'Rp' . number_format($discount->discount_amount, 0, ',', '.'),
                ];
            }
    
            return [
                'key' => $key,
                'qty' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $item['qty'] * $item['price'],
                'name' => $name,
                'model' => $model,
                'size' => $size?->name ?? ($type === 'minuman' ? '-' : null),
                'sugar' => $sugar?->level ?? ($type === 'minuman' ? '-' : null),
                'topping' => $topping?->nama ?? '-',
                'regular_price' => $regularPrice,
                'discount_info' => $discountInfo,
                'has_discount' => $discountInfo !== null,
                'type' => $type,
            ];
        });
    
        $total = $detailedCart->sum('subtotal');
        
        // Calculate rounded total (down to nearest 1000)
        $roundedTotal = floor($total / 1000) * 1000;
        $roundingAmount = $roundedTotal - $total;
        
        // Get web settings for order mode
        $webSettings = $this->web_settings ?: WebSetting::first();
    
        return view('livewire.cart-page', [
            'cartItems' => $detailedCart,
            'total' => $roundedTotal, // Use rounded total as the main total
            'originalTotal' => $total, // Keep original total for reference
            'roundingAmount' => $roundingAmount, // The amount rounded (can be positive or negative)
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
            $type = $item['type'] ?? 'minuman'; // Default to 'minuman' for backward compatibility
            
            if ($type === 'minuman') {
                $model = Minuman::find($item['id']);
                // Safely access array keys with null coalescing operator
                $sizeId = $item['size_id'] ?? null;
                $sugarId = $item['sugar_id'] ?? null;
                $toppingId = $item['topping_id'] ?? null;
                
                $size = $sizeId ? Size::find($sizeId) : null;
                $sugar = $sugarId ? Sugar::find($sugarId) : null;
                $topping = $toppingId ? Topping::find($toppingId) : null;
                $name = $model?->nama ?? 'Unknown Drink';
                $modelType = 'minuman';
            } else {
                $model = Makanan::find($item['id']);
                $toppingId = $item['topping_id'] ?? null;
                $topping = $toppingId ? Topping::find($toppingId) : null;
                $name = $model?->nama ?? 'Unknown Food';
                $modelType = 'makanan';
                $size = null;
                $sugar = null;
            }
    
            return [
                'key' => $key,
                'model_id' => $item['id'],
                'model_type' => $modelType,
                'qty' => $item['qty'],
                'harga' => $item['price'],
                'subtotal' => $item['qty'] * $item['price'],
                'name' => $name,
                'size' => $type === 'minuman' ? ($size?->name ?? null) : null,
                'gula' => $type === 'minuman' ? ($sugar?->level ?? null) : null,
                'topping' => $topping?->nama ?? null,
                'catatan' => $item['catatan'] ?? null,
                'type' => $type,
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
            $detailData = [
                'harga' => $item['harga'],
                'qty' => $item['qty'],
                'topping' => $item['topping'],
                'catatan' => $item['catatan'],
                'subtotal' => $item['subtotal'],
            ];
            
            // Handle both food and drink items with the updated schema
            if ($item['type'] === 'minuman') {
                $detailData['minuman_id'] = $item['model_id'];
                $detailData['nama_minuman'] = $item['name'];
                $detailData['makanan_id'] = null;
                $detailData['nama_makanan'] = null;
                $detailData['size'] = $item['size'] ?? null;
                $detailData['gula'] = $item['gula'] ?? null;
            } else {
                // For food items, use makanan_id and nama_makanan
                $detailData['makanan_id'] = $item['model_id'];
                $detailData['nama_makanan'] = $item['name'];
                $detailData['minuman_id'] = null;
                $detailData['nama_minuman'] = null;
                $detailData['size'] = null;
                $detailData['gula'] = null;
            }
            
            $pesanan->details()->create($detailData);
        }
    
        // Prepare WhatsApp message
        $message = "Assalamualaikum, saya ingin pesan:\n\n";
        foreach ($cartItems as $item) {
            $message .= "- {$item['name']}";
            
            // Only show size and sugar for drinks
            if ($item['type'] === 'minuman') {
                $message .= $item['size'] ? " (Size: {$item['size']}" : "";
                $message .= $item['gula'] ? ", Gula: {$item['gula']}" : "";
                $message .= $item['size'] ? ")" : "";
            }
            
            // Show topping if available for both food and drinks
            $message .= $item['topping'] ? ", Topping: {$item['topping']}" : "";
            
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
