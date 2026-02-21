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
    public $makanans = []; // Tambah property untuk makanan

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
        })->orderBy('nama', 'asc')->get();
        $this->makanans = \App\Models\Makanan::when($this->filterKategori, function ($query) {
            $query->where('kategori', $this->filterKategori);
        })->orderBy('nama', 'asc')->get();
    }

    public function render()
    {
        $webSettings = \App\Models\WebSetting::first();
        
        return view('livewire.daftar-minuman', [
            'theme' => $webSettings ? $webSettings->theme : 'green',
            'web_settings' => $webSettings,
            'makanans' => $this->makanans,
        ]);
    }
}
