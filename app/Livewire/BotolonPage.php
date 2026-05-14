<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BotolanProduk;
use App\Models\BotolanUkuran;
use App\Models\WebSetting;

class BotolonPage extends Component
{
    public ?int $selectedProdukId = null;
    public ?int $selectedUkuranId = null;

    public function mount(): void
    {
        $produkId = request()->integer('produk');
        if ($produkId) {
            $exists = BotolanProduk::where('id', $produkId)->where('is_active', true)->exists();
            if ($exists) {
                $this->selectedProdukId = $produkId;
            }
        }
    }

    public function updatedSelectedProdukId(): void
    {
        $this->selectedUkuranId = null;
    }

    public function addToCart(): void
    {
        if (!$this->selectedProdukId || !$this->selectedUkuranId) {
            session()->flash('error', 'Pilih minuman dan ukuran terlebih dahulu.');
            return;
        }

        $produk = BotolanProduk::with(['minuman', 'ukurans'])->find($this->selectedProdukId);
        $ukuran = BotolanUkuran::find($this->selectedUkuranId);

        if (!$produk || !$ukuran) {
            session()->flash('error', 'Produk tidak ditemukan.');
            return;
        }

        $key  = 'botolan-' . $produk->id . '-' . $ukuran->id;
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            $cart[$key]['qty']++;
        } else {
            $cart[$key] = [
                'type'      => 'botolan',
                'id'        => $produk->id,
                'ukuran_id' => $ukuran->id,
                'nama'      => $produk->minuman->nama . ' Botolan ' . $ukuran->label,
                'price'     => $ukuran->harga,
                'qty'       => 1,
                'catatan'   => null,
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cartUpdated');
        session()->flash('success', 'Berhasil ditambahkan ke keranjang!');
    }

    public function render()
    {
        $availableProduks = BotolanProduk::where('is_active', true)
            ->with(['minuman', 'ukurans'])
            ->whereHas('ukurans', fn($q) => $q->where('is_active', true))
            ->get()
            ->sortBy(fn($p) => $p->minuman?->nama)
            ->values();

        $selectedProduk = $this->selectedProdukId
            ? BotolanProduk::with(['minuman', 'media', 'ukurans' => fn($q) => $q->where('is_active', true)->with('media')])->find($this->selectedProdukId)
            : null;

        $selectedUkuran = $this->selectedUkuranId
            ? BotolanUkuran::with('media')->find($this->selectedUkuranId)
            : null;

        return view('livewire.botolan-page', [
            'web_settings'     => WebSetting::first(),
            'availableProduks' => $availableProduks,
            'selectedProduk'   => $selectedProduk,
            'selectedUkuran'   => $selectedUkuran,
        ])->layout('layouts.public');
    }
}
