<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Minuman;
use App\Models\Makanan;
use App\Models\Size;
use App\Models\Sugar;
use App\Models\Topping;
use App\Models\WebSetting;
use App\Models\DiscountCode;
use App\Models\DiscountCodeUsage;

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
    public $discount_code = '';
    public $applied_discount = null;
    public $discount_amount = 0;
    public $discount_error = '';

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
        
        // Apply discount if valid
        $this->discount_amount = 0;
        if ($this->applied_discount) {
            $this->discount_amount = $this->applied_discount->calculateDiscount($originalTotal);
        }
        
        $totalAfterDiscount = $originalTotal - $this->discount_amount;
        
        // Round down to nearest 1000
        $this->total = floor($totalAfterDiscount / 1000) * 1000;
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
        
        // Clear discount code when cart is cleared
        $this->clearDiscountCode();
    }
    
    public function applyDiscountCode()
    {
        $this->discount_error = '';
        
        if (empty($this->discount_code)) {
            $this->discount_error = 'Masukkan kode diskon terlebih dahulu.';
            return;
        }

        $discountCode = DiscountCode::where('code', strtoupper(trim($this->discount_code)))->first();

        if (!$discountCode) {
            $this->discount_error = 'Kode diskon tidak valid.';
            return;
        }

        $cartTotal = collect($this->cart)->sum(fn($item) => $item['qty'] * $item['price']);

        if (!$discountCode->canBeApplied($cartTotal)) {
            if (!$discountCode->isActive()) {
                $this->discount_error = 'Kode diskon sudah tidak aktif atau telah kadaluarsa.';
            } elseif ($cartTotal < $discountCode->minimum_purchase) {
                $this->discount_error = 'Minimum pembelian Rp ' . number_format($discountCode->minimum_purchase, 0, ',', '.') . ' untuk menggunakan kode ini.';
            } else {
                $this->discount_error = 'Kode diskon tidak dapat digunakan.';
            }
            return;
        }
        
        $this->applied_discount = $discountCode;
        $this->updateTotal();
        $this->dispatch('discount-applied');
    }
    
    public function clearDiscountCode()
    {
        $this->discount_code = '';
        $this->applied_discount = null;
        $this->discount_amount = 0;
        $this->discount_error = '';
        $this->updateTotal();
    }
    
    public function updatedDiscountCode()
    {
        // Clear discount error when user types
        $this->discount_error = '';
        
        // Auto-apply if discount was previously applied and user changes the code
        if ($this->applied_discount && strtoupper($this->discount_code) !== $this->applied_discount->code) {
            $this->applied_discount = null;
            $this->discount_amount = 0;
            $this->updateTotal();
        }
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
        // Preload semua data sekaligus untuk menghindari N+1 queries
        // Item minuman lama mungkin tidak punya field 'type', default ke 'minuman'
        $cart = $this->cart;
        $minumanIds = collect($cart)->filter(fn($i) => ($i['type'] ?? 'minuman') === 'minuman')->pluck('id')->unique()->filter()->values()->all();
        $makananIds  = collect($cart)->filter(fn($i) => ($i['type'] ?? 'minuman') === 'makanan')->pluck('id')->unique()->filter()->values()->all();
        $sizeIds    = collect($cart)->pluck('size_id')->unique()->filter()->values()->all();
        $sugarIds   = collect($cart)->pluck('sugar_id')->unique()->filter()->values()->all();
        $toppingIds = collect($cart)->pluck('topping_id')->unique()->filter()->values()->all();

        $minumans = $minumanIds ? Minuman::whereIn('id', $minumanIds)->get()->keyBy('id') : collect();
        $makanans = $makananIds ? Makanan::whereIn('id', $makananIds)->get()->keyBy('id') : collect();
        $sizes    = $sizeIds    ? Size::whereIn('id', $sizeIds)->get()->keyBy('id')       : collect();
        $sugars   = $sugarIds   ? Sugar::whereIn('id', $sugarIds)->get()->keyBy('id')     : collect();
        $toppings = $toppingIds ? Topping::whereIn('id', $toppingIds)->get()->keyBy('id') : collect();

        $detailedCart = collect($cart)->map(function ($item, $key) use ($minumans, $makanans, $sizes, $sugars, $toppings) {
            $type = $item['type'] ?? 'minuman';

            if ($type === 'minuman') {
                $model  = $minumans[$item['id']] ?? null;
                $size   = isset($item['size_id'])   ? ($sizes[$item['size_id']]     ?? null) : null;
                $sugar  = isset($item['sugar_id'])  ? ($sugars[$item['sugar_id']]   ?? null) : null;
                $topping = isset($item['topping_id']) ? ($toppings[$item['topping_id']] ?? null) : null;
                $name   = $model?->nama ?? 'Unknown Drink';
            } else {
                $model  = $makanans[$item['id']] ?? null;
                $topping = isset($item['topping_id']) ? ($toppings[$item['topping_id']] ?? null) : null;
                $name   = $model?->nama ?? 'Unknown Food';
                $size   = null;
                $sugar  = null;
            }

            $regularPrice = null;
            $discountInfo = null;

            if ($model && $model->activeDiscount()) {
                $discount  = $model->activeDiscount();
                $basePrice = $type === 'minuman'
                    ? ($model->harga ?? $model->base_price ?? 0)
                    : ($model->base_price ?? 0);

                if ($type === 'minuman') {
                    if ($size)    $basePrice += $size->price;
                    if ($sugar)   $basePrice += $sugar->price;
                }
                if ($topping) $basePrice += $topping->default_price;

                $regularPrice = $basePrice;
                $discountInfo = [
                    'name'          => $discount->name,
                    'type'          => $discount->discount_type,
                    'amount'        => $discount->discount_amount,
                    'discount_text' => $discount->discount_type === 'percentage'
                        ? (int)$discount->discount_amount . '%'
                        : 'Rp' . number_format($discount->discount_amount, 0, ',', '.'),
                ];
            }

            return [
                'key'          => $key,
                'qty'          => $item['qty'],
                'price'        => $item['price'],
                'subtotal'     => $item['qty'] * $item['price'],
                'name'         => $name,
                'model'        => $model,
                'size'         => $size?->name ?? ($type === 'minuman' ? '-' : null),
                'sugar'        => $sugar?->level ?? ($type === 'minuman' ? '-' : null),
                'topping'      => $topping?->nama ?? '-',
                'regular_price' => $regularPrice,
                'discount_info' => $discountInfo,
                'has_discount'  => $discountInfo !== null,
                'type'          => $type,
            ];
        });
    
        $total = $detailedCart->sum('subtotal');
        
        // Apply discount if valid
        $discountAmount = 0;
        if ($this->applied_discount) {
            $discountAmount = $this->applied_discount->calculateDiscount($total);
        }
        
        $totalAfterDiscount = $total - $discountAmount;
        
        // Calculate rounded total (down to nearest 1000)
        $roundedTotal = floor($totalAfterDiscount / 1000) * 1000;
        $roundingAmount = $roundedTotal - $totalAfterDiscount;
        
        // Get web settings for order mode
        $webSettings = $this->web_settings ?: WebSetting::first();
    
        return view('livewire.cart-page', [
            'cartItems' => $detailedCart,
            'total' => $roundedTotal, // Use rounded total as the main total
            'originalTotal' => $total, // Keep original total for reference
            'totalAfterDiscount' => $totalAfterDiscount, // Total after discount but before rounding
            'discountAmount' => $discountAmount, // Discount amount applied
            'roundingAmount' => $roundingAmount, // The amount rounded (can be positive or negative)
            'orderMode' => $webSettings->order_mode,
            'web_settings' => $webSettings
        ])->layout('layouts.public');
    }
    public function konfirmasiCheckout()
    {
        if (empty($this->cart)) {
            $this->addError('nama_pemesan', 'Keranjang belanja kosong.');
            return;
        }

        $validationRules = [
            'nama_pemesan'    => 'required',
            'waktu_pengantaran' => 'required',
        ];

        if ($this->order_type === 'delivery') {
            $validationRules['alamat_pengantaran'] = 'required';
        }

        $this->validate($validationRules);

        // Preload semua data sekaligus untuk menghindari N+1 queries
        // Item minuman lama mungkin tidak punya field 'type', default ke 'minuman'
        $minumanIds = collect($this->cart)->filter(fn($i) => ($i['type'] ?? 'minuman') === 'minuman')->pluck('id')->unique()->filter()->values()->all();
        $makananIds  = collect($this->cart)->filter(fn($i) => ($i['type'] ?? 'minuman') === 'makanan')->pluck('id')->unique()->filter()->values()->all();
        $sizeIds    = collect($this->cart)->pluck('size_id')->unique()->filter()->values()->all();
        $sugarIds   = collect($this->cart)->pluck('sugar_id')->unique()->filter()->values()->all();
        $toppingIds = collect($this->cart)->pluck('topping_id')->unique()->filter()->values()->all();

        $minumans = $minumanIds ? Minuman::whereIn('id', $minumanIds)->get()->keyBy('id') : collect();
        $makanans = $makananIds  ? Makanan::whereIn('id', $makananIds)->get()->keyBy('id')  : collect();
        $sizes    = $sizeIds    ? Size::whereIn('id', $sizeIds)->get()->keyBy('id')         : collect();
        $sugars   = $sugarIds   ? Sugar::whereIn('id', $sugarIds)->get()->keyBy('id')       : collect();
        $toppings = $toppingIds ? Topping::whereIn('id', $toppingIds)->get()->keyBy('id')   : collect();

        $cartItems = collect($this->cart)->map(function ($item, $key) use ($minumans, $makanans, $sizes, $sugars, $toppings) {
            $type = $item['type'] ?? 'minuman';

            if ($type === 'minuman') {
                $model     = $minumans[$item['id']] ?? null;
                $size      = isset($item['size_id'])    ? ($sizes[$item['size_id']]      ?? null) : null;
                $sugar     = isset($item['sugar_id'])   ? ($sugars[$item['sugar_id']]    ?? null) : null;
                $topping   = isset($item['topping_id']) ? ($toppings[$item['topping_id']] ?? null) : null;
                $name      = $model?->nama ?? 'Unknown Drink';
                $modelType = 'minuman';
            } else {
                $model     = $makanans[$item['id']] ?? null;
                $topping   = isset($item['topping_id']) ? ($toppings[$item['topping_id']] ?? null) : null;
                $name      = $model?->nama ?? 'Unknown Food';
                $modelType = 'makanan';
                $size      = null;
                $sugar     = null;
            }

            return [
                'key'       => $key,
                'model_id'  => $item['id'],
                'model_type' => $modelType,
                'qty'       => $item['qty'],
                'harga'     => $item['price'],
                'subtotal'  => $item['qty'] * $item['price'],
                'name'      => $name,
                'size'      => $type === 'minuman' ? ($size?->name ?? null) : null,
                'gula'      => $type === 'minuman' ? ($sugar?->level ?? null) : null,
                'topping'   => $topping?->nama ?? null,
                'catatan'   => $item['catatan'] ?? null,
                'type'      => $type,
            ];
        });
    
        // Generate or get session ID
        $sessionId = session()->getId();
        
        // Store session ID in cookie for persistence
        \Illuminate\Support\Facades\Cookie::queue('user_session', $sessionId, 60 * 24 * 30); // 30 days
        
        // Calculate totals
        $cartTotal = collect($this->cart)->sum(function ($item) {
            return $item['qty'] * $item['price'];
        });
        
        $discountAmount = 0;
        $discountCodeId = null;
        
        if ($this->applied_discount) {
            $discountAmount = $this->applied_discount->calculateDiscount($cartTotal);
            $discountCodeId = $this->applied_discount->id;
            
            // Apply the discount code (increment usage)
            $this->applied_discount->apply();
        }
        
        $totalAfterDiscount = $cartTotal - $discountAmount;
        $finalTotal = floor($totalAfterDiscount / 1000) * 1000;
        
        // Create the order
        $pesanan = \App\Models\Pesanan::create([
            'user_id' => auth()->id(),
            'session_id' => $sessionId,
            'nama_pemesan' => $this->nama_pemesan,
            'alamat_pengantaran' => $this->order_type === 'delivery' ? $this->alamat_pengantaran : 'Takeaway',
            'waktu_pengantaran' => $this->waktu_pengantaran,
            'catatan' => $this->catatan ?? null,
            'total' => (int) $finalTotal,
            'total_harga' => $finalTotal,
            'discount_code_id' => $discountCodeId,
            'discount_amount' => $discountAmount,
            'status' => 'menunggu_konfirmasi',
            'order_type' => $this->order_type,
            'nomor_pesanan' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(uniqid())
        ]);
        
        // Create discount usage record with order ID
        if ($discountCodeId) {
            DiscountCodeUsage::create([
                'discount_code_id' => $discountCodeId,
                'pesanan_id' => $pesanan->id,
                'discount_amount' => $discountAmount,
                'original_total' => $cartTotal,
            ]);
        }
        
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
    
        $message .= "\nSubtotal: Rp " . number_format($cartTotal, 0, ',', '.');
        
        if ($discountAmount > 0) {
            $message .= "\nDiscount ({$this->applied_discount->code}): -Rp " . number_format($discountAmount, 0, ',', '.');
        }
        
        $message .= "\nTotal: Rp " . number_format($finalTotal, 0, ',', '.');
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
        
        // Clear discount code
        $this->clearDiscountCode();
        
        // Redirect to WhatsApp
        $waRaw = WebSetting::first()->whatsapp_number ?? '';
        $wa = preg_replace('/[^0-9]/', '', $waRaw);
        if (empty($wa)) {
            session()->flash('error', 'Nomor WhatsApp belum dikonfigurasi.');
            return;
        }
        return redirect()->away("https://wa.me/{$wa}?text=" . urlencode($message));
    }
}
