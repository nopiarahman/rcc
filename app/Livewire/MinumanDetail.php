<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Minuman;
use App\Models\WebSetting;

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

    public $theme;
    
    public function mount($id)
    {
        $this->minuman = Minuman::with(['sizes', 'sugars', 'toppings'])->findOrFail($id);
    
        $this->sizes = $this->minuman->sizes;
        $this->sugars = $this->minuman->sugars;
        $this->toppings = $this->minuman->toppings;
    
        $this->selectedSizeId = $this->minuman->default_size_id;
        $this->selectedSugarId = $this->minuman->default_sugar_id;
        $this->selectedToppingId = $this->minuman->default_topping_id;
        
        // Get theme from web settings
        $webSetting = WebSetting::first();
        $this->theme = $webSetting ? $webSetting->theme : 'green';
    }
    // Metode untuk menambahkan produk ke keranjang
    public function getTotalPriceProperty()
    {
        $size = collect($this->sizes)->firstWhere('id', $this->selectedSizeId);
        $sugar = collect($this->sugars)->firstWhere('id', $this->selectedSugarId);
        $topping = collect($this->toppings)->firstWhere('id', $this->selectedToppingId);
        
        // Use discounted price if available, otherwise use base price
        $basePrice = $this->minuman->activeDiscount() 
            ? $this->minuman->discounted_price 
            : $this->minuman->base_price;
    
        return $basePrice
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
        $this->dispatch('cartUpdated');
        // Menampilkan pesan sukses ke pengguna
        session()->flash('message', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function getThemeGradient($theme = null)
    {
        $theme = $theme ?: $this->theme;
        
        $gradients = [
            'green' => 'linear-gradient(135deg, #011a0f, #006a3e)',
            'brown' => 'linear-gradient(135deg, #3e2723, #6d4c41)',
            'yellow' => 'linear-gradient(135deg, #f57f17, #ffd600)',
            'blue' => 'linear-gradient(135deg, #0d47a1, #2196f3)',
            'orange' => 'linear-gradient(135deg, #e65100, #ff9800)',
        ];
        
        return $gradients[$theme] ?? $gradients['green'];
    }
    
    public function getThemeColorFromGradient($theme = null)
    {
        $gradient = $this->getThemeGradient($theme);
        if (preg_match('/#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})/', $gradient, $matches)) {
            return $matches[0]; // Return the first hex color found in the gradient
        }
        return '#006a3e'; // Default color if no hex found
    }
    
    public function getThemeColor($theme = null)
    {
        $theme = $theme ?: $this->theme;
        
        $colors = [
            'green' => 'success',
            'brown' => 'brown',
            'yellow' => 'warning',
            'blue' => 'primary',
            'orange' => 'orange',
        ];
        
        return $colors[$theme] ?? 'success';
    }
    
    public function getThemeTextColor($theme = null)
    {
        $theme = $theme ?: $this->theme;
        
        $colors = [
            'green' => 'success',
            'brown' => 'brown',
            'yellow' => 'warning',
            'blue' => 'primary',
            'orange' => 'orange',
        ];
        
        return $colors[$theme] ?? 'success';
    }
    
    public function isDarkColor($color) 
    {
        // Remove any '#' from the color string
        $color = str_replace('#', '', $color);
        
        // Convert shorthand color to full format
        if (strlen($color) === 3) {
            $color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
        }
        
        // Get RGB values
        $r = hexdec(substr($color, 0, 2));
        $g = hexdec(substr($color, 2, 2));
        $b = hexdec(substr($color, 4, 2));
        
        // Calculate brightness using the YIQ formula
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        
        // Return true if the color is dark
        return $yiq < 128;
    }
    
    public function render()
    {
        $webSettings = \App\Models\WebSetting::first();
        
        return view('livewire.minuman-detail', [
            'web_settings' => $webSettings
        ])->layout('layouts.public');
    }
}