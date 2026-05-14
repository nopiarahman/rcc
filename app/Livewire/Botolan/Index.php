<?php

namespace App\Livewire\Botolan;

use Livewire\Component;
use App\Models\BotolanProduk;

class Index extends Component
{
    public function hapus(int $id): void
    {
        $produk = BotolanProduk::findOrFail($id);
        $produk->clearMediaCollection('foto');
        $produk->delete();
        session()->flash('success', 'Botolan berhasil dihapus!');
    }

    public function toggleActive(int $id): void
    {
        $produk = BotolanProduk::findOrFail($id);
        $produk->update(['is_active' => !$produk->is_active]);
    }

    public function render()
    {
        return view('livewire.botolan.index', [
            'botolans' => BotolanProduk::with(['minuman', 'allUkurans'])->latest()->get(),
        ]);
    }
}
