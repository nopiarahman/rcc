<?php

namespace App\Livewire\Makanan;

use Livewire\Component;
use App\Models\Makanan;

class Index extends Component
{
    public $makanans;
    public $allKategoris = [];
    public $filterKategori = '';

    public function onKategoriChanged($value)
    {
        $this->filterKategori = $value;
        $this->loadData();
    }

    public function loadData()
    {
        $this->makanans = Makanan::when($this->filterKategori, function ($query) {
            $query->where('kategori', $this->filterKategori);
        })->with(['bahans', 'toppings'])->latest()->get();
    }

    public function mount()
    {
        $this->loadData();
        $this->allKategoris = Makanan::distinct()->pluck('kategori')->filter()->values()->toArray();
    }

    public function hapus($id)
    {
        $makanan = Makanan::findOrFail($id);
        $makanan->delete();
        $this->loadData();
        session()->flash('success', 'Makanan berhasil dihapus!');
    }

    public function render()
    {
        return view('livewire.makanan.index');
    }
}
