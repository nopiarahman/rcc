<?php
namespace App\Livewire\Minuman;

use Livewire\Component;
use App\Models\Minuman;

class Index extends Component
{
    public $filterKategori = '';
    public $allKategoris = [];

    public $minumans = [];

    public function onKategoriChanged($value)
    {
        $this->filterKategori = $value;
        $this->muatData();
    }
    
    public function mount()
    {
        $this->muatData();
        $this->allKategoris = Minuman::distinct()->pluck('kategori')->filter()->values()->toArray();
    }
    
    public function muatData()
    {
        $this->minumans = Minuman::when($this->filterKategori, function ($query) {
            $query->where('kategori', $this->filterKategori);
        })->latest()->get();
    }
    
    public function render()
    {
        return view('livewire.minuman.index');
    }
    // public function getFilteredMinumansProperty()
    // {
    //     return Minuman::when($this->filterKategori, function ($query) {
    //         $query->where('kategori', $this->filterKategori);
    //     })->latest()->get();
    // }
};