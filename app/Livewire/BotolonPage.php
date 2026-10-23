<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BotolanProduk;
use App\Models\BotolanUkuran;
use App\Models\WebSetting;
use App\Services\CartService;

class BotolonPage extends Component
{
    public ?int $selectedProdukId = null;
    public ?int $selectedUkuranId = null;

    public function mount(): void
    {
        $produkId = request()->integer('produk');
        if ($produkId) {
            $exists = BotolanProduk::where('id', $produkId)
                ->where('is_active', true)
                ->whereHas('minuman', fn($q) => $q->where('is_habis', false))
                ->exists();
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

        $produk = BotolanProduk::with(['minuman', 'ukurans'])->where('is_active', true)->find($this->selectedProdukId);
        // Ukuran harus milik produk yang dipilih dan masih aktif
        $ukuran = $produk?->ukurans->where('is_active', true)->firstWhere('id', $this->selectedUkuranId);

        if (!$produk || !$ukuran) {
            session()->flash('error', 'Produk tidak ditemukan.');
            return;
        }

        if ($produk->minuman?->is_habis) {
            session()->flash('error', 'Maaf, minuman ini sedang habis.');
            $this->selectedProdukId = null;
            $this->selectedUkuranId = null;
            return;
        }

        // Keranjang hanya menyimpan ID & pilihan; harga selalu dihitung ulang di server
        $cart = CartService::add(session()->get('cart', []), [
            'type' => 'botolan',
            'id' => $produk->id,
            'ukuran_id' => $ukuran->id,
            'qty' => 1,
        ]);

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
            ? $selectedProduk?->ukurans->firstWhere('id', $this->selectedUkuranId)
            : null;

        return view('livewire.botolan-page', [
            'web_settings'     => WebSetting::current(),
            'availableProduks' => $availableProduks,
            'selectedProduk'   => $selectedProduk,
            'selectedUkuran'   => $selectedUkuran,
        ])->layout('layouts.public')->title('Pesan Botolan');
    }
}
