<?php

namespace App\Livewire;

use App\Models\Minuman;
use Livewire\Component;
use App\Helpers\DrinkPriceHelper;
class DaftarMinuman extends Component
    {
    public $filterKategori = '';
    public $allKategoris = [];
    public $minumans = [];

    protected $listeners = ['gantiKategori'];

    public function mount($kategoris)
    {
        $this->allKategoris = $kategoris;
        $this->getMinumans();
    }

    public function gantiKategori($kategori)
    {
        $this->filterKategori = $kategori;
        $this->getMinumans();
    }
    public function openDetailModal($minumanId)
    {
        return redirect()->route('minuman.detail', ['minuman' => $minumanId]);
    }
    public function getMinumans()
    {
        $this->minumans = Minuman::when($this->filterKategori, function ($query) {
            $query->where('kategori', $this->filterKategori);
        })->get();
    }

}
