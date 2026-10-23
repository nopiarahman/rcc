<?php

namespace App\Livewire;

use App\Models\Pesanan;
use App\Models\WebSetting;
use Livewire\Component;

class PesananBerhasil extends Component
{
    public string $nomor;

    public function mount(string $nomor): void
    {
        // Hanya pemilik pesanan (sesi/cookie riwayat/akun) yang boleh melihat halaman ini
        Pesanan::ownedByVisitor()->where('nomor_pesanan', $nomor)->firstOrFail();
        $this->nomor = $nomor;
    }

    public function render()
    {
        $pesanan = Pesanan::ownedByVisitor()
            ->with(['details', 'discountCode', 'voucher'])
            ->where('nomor_pesanan', $this->nomor)
            ->firstOrFail();

        return view('livewire.pesanan-berhasil', [
            'pesanan' => $pesanan,
            'whatsappUrl' => $pesanan->whatsappUrl(),
            'web_settings' => WebSetting::current(),
        ])->layout('layouts.public', ['whiteBackground' => true])->title('Pesanan Diterima');
    }
}
