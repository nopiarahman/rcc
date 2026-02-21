<?php

namespace App\Livewire;

use App\Models\Minuman;
use Livewire\Component;

use App\Models\Banner;

class MenuPage extends Component
{
    public $filterKategori = '';
    public $allKategoris = [];
    public $allKategoriMakanan = [];
    public $minumans = [];
    public $makanans = [];
    public $banners = [];

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
