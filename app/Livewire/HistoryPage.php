<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pesanan;
use App\Models\WebSetting;

class HistoryPage extends Component
{
    public $selectedOrder = null;

    public function viewOrder($orderId)
    {
        // Hanya pesanan milik pengunjung ini (sesi, cookie riwayat, atau akun customer)
        $this->selectedOrder = Pesanan::ownedByVisitor()->with('details')->findOrFail($orderId);
        $this->dispatch('show-order-modal');
    }

    public function showOrderDetails($orderId)
    {
        $this->selectedOrder = Pesanan::ownedByVisitor()->with('details')->findOrFail($orderId);
        $this->dispatch('show-order-details');
    }

    public function render()
    {
        return view('livewire.history-page', [
            'orders' => Pesanan::ownedByVisitor()->with('details')->latest()->take(50)->get(),
            'selectedOrder' => $this->selectedOrder,
            'web_settings' => WebSetting::current(),
        ])->layout('layouts.public')->title('Riwayat Pesanan');
    }
}
