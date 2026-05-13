<?php

namespace App\Livewire;

use App\Models\Minuman;
use Livewire\Component;

use App\Models\Banner;
use App\Models\Popup;

class MenuPage extends Component
{
    public $filterKategori = '';
    public $allKategoris = [];
    public $allKategoriMakanan = [];
    public $minumans = [];
    public $makanans = [];
    public $banners = [];
    public $popup = null;

    public function mount()
    {
        $this->muatData();
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
        $this->banners = Banner::where('status', true)
            ->orderBy('order')
            ->get();
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

    public function gantiKategori($kategori)
    {
        $this->filterKategori = $kategori;
        $this->muatData();
    }

    public function muatData()
    {
        $this->minumans = Minuman::when($this->filterKategori, function ($query) {
            $query->where('kategori', $this->filterKategori);
        })->orderBy('nama', 'asc')->get();
        $this->makanans = \App\Models\Makanan::when($this->filterKategori, function ($query) {
            $query->where('kategori', $this->filterKategori);
        })->orderBy('nama', 'asc')->get();
    }

    public function render()
    {
        $webSettings = \App\Models\WebSetting::first();
        
        return view('livewire.menu-page', [
            'minumans' => $this->minumans,
            'makanans' => $this->makanans,
            'web_settings' => $webSettings
        ])->layout('layouts.public');
    }
}
