<?php

namespace App\Livewire;

use App\Models\Makanan;
use App\Models\Minuman;
use Livewire\Component;

class DaftarMinuman extends Component
{
    public $filterKategori = '';
    public $allKategoris = [];

    protected $listeners = ['gantiKategori'];

    public function mount($kategoris)
    {
        $this->allKategoris = $kategoris;
    }

    public function gantiKategori($kategori)
    {
        $this->filterKategori = $kategori;
    }

    public function render()
    {
        $webSettings = \App\Models\WebSetting::current();

        // Eager load relasi yang dipakai kartu menu (foto, diskon aktif, opsi untuk harga default) agar tidak N+1
        $minumans = Minuman::with(['media', 'discounts', 'sizes', 'sugars', 'toppings'])
            ->when($this->filterKategori, fn ($query) => $query->where('kategori', $this->filterKategori))
            ->orderBy('nama', 'asc')
            ->get();

        $makanans = Makanan::with(['media', 'discounts'])
            ->when($this->filterKategori, fn ($query) => $query->where('kategori', $this->filterKategori))
            ->orderBy('nama', 'asc')
            ->get();

        return view('livewire.daftar-minuman', [
            'theme' => $webSettings ? $webSettings->theme : 'green',
            'web_settings' => $webSettings,
            'minumans' => $minumans,
            'makanans' => $makanans,
        ]);
    }
}
