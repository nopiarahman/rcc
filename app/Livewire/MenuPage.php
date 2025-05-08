<?php

namespace App\Livewire;

use App\Models\Minuman;
use Livewire\Component;

class MenuPage extends Component
{
    public $filterKategori = '';
    public $allKategoris = [];
    public $minumans = [];

    public function mount()
    {
        $this->muatData();
        $this->allKategoris = Minuman::distinct()
            ->pluck('kategori')
            ->filter()
            ->values()
            ->toArray();
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