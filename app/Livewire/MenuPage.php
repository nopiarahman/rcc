<?php

namespace App\Livewire;

use App\Models\Minuman;
use App\Models\BotolanProduk;
use Livewire\Component;
use App\Models\Banner;
use App\Models\Popup;

class MenuPage extends Component
{
    public $allKategoris = [];
    public $allKategoriMakanan = [];
    public $popup = null;

    public function mount()
    {
        // Daftar produk dimuat oleh komponen DaftarMinuman; di sini hanya kategori, banner, botolan & popup
        $this->allKategoris = Minuman::distinct()
            ->pluck('kategori')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
        $this->allKategoriMakanan = \App\Models\Makanan::distinct()
            ->pluck('kategori')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
        $today = now()->toDateString();
        $this->popup = Popup::where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->with('media')
            ->orderBy('order')
            ->first();
    }

    public function render()
    {
        $webSettings = \App\Models\WebSetting::current();
        $banners = Banner::with('media')->where('status', true)->orderBy('order')->get();
        $botolans = BotolanProduk::where('is_active', true)
            ->with(['minuman', 'ukurans', 'media'])
            ->whereHas('ukurans', fn($q) => $q->where('is_active', true))
            ->get()
            ->sortBy(fn($p) => $p->minuman?->nama)
            ->values();

        return view('livewire.menu-page', [
            'botolans'    => $botolans,
            'banners'     => $banners,
            'web_settings' => $webSettings
        ])->layout('layouts.public')->title('Menu');
    }
}
