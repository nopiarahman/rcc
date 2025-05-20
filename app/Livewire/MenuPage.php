<?php

namespace App\Livewire;

use App\Models\Minuman;
use Livewire\Component;

use App\Models\Banner;

class MenuPage extends Component
{
    public $filterKategori = '';
    public $allKategoris = [];
    public $minumans = [];
    public $banners = [];

    public function mount()
    {
        $this->muatData();
        $this->allKategoris = Minuman::distinct()
            ->pluck('kategori')
            ->filter()
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
        })->latest()->get();
    }

    public function render()
    {
        return view('livewire.menu-page',['minumans'=>$this->minumans])->layout('layouts.public');
    }
}