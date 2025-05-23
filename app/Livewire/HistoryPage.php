<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;

class HistoryPage extends Component
{
    public $openOrderId = null;
    public $orders;
    public $selectedOrder = null;

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        // Get session ID from session or cookie
        $sessionId = session()->getId() ?? request()->cookie('user_session');
        
        if ($sessionId) {
            $this->orders = Pesanan::with('details')
                ->where('session_id', $sessionId)
                ->orWhere('user_id', auth()->id())
                ->latest()
                ->get();
        } else {
            $this->orders = collect();
        }
    }
    
    public function viewOrder($orderId)
    {
        $this->selectedOrder = Pesanan::with('details')->find($orderId);
        $this->dispatch('show-order-modal');
    }

    public function cancelOrder($orderId)
    {
        $order = Pesanan::findOrFail($orderId);
        
        if ($order->status !== 'menunggu_konfirmasi') {
            $this->dispatch('show-message', [
                'type' => 'error',
                'message' => 'Pesanan tidak dapat dibatalkan karena status sudah ' . $this->getStatusText($order->status)
            ]);
            return;
        }
        
        $order->update(['status' => 'dibatalkan']);
        $this->loadOrders();
        
        if ($this->selectedOrder && $this->selectedOrder->id == $orderId) {
            $this->selectedOrder = $order->fresh();
        }
        
        $this->dispatch('show-message', [
            'type' => 'success',
            'message' => 'Pesanan berhasil dibatalkan'
        ]);
    }
    
    private function getStatusText($status)
    {
        $statuses = [
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'diproses' => 'Diproses',
            'diantar' => 'Diantar',
            'dikirim' => 'Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];
        
        return $statuses[$status] ?? ucfirst($status);
    }

    public function showOrderDetails($orderId)
    {
        $this->selectedOrder = Pesanan::with('details')->findOrFail($orderId);
        $this->dispatch('show-order-details');
    }

    public function render()
    {
        return view('livewire.history-page', [
            'orders' => $this->orders,
            'selectedOrder' => $this->selectedOrder,
        ])->layout('layouts.public');
    }
}
