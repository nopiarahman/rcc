<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pesanan;
use App\Models\Voucher;
use App\Models\WebSetting;
use Illuminate\Support\Facades\Auth;

class CustomerAccount extends Component
{
    use WithPagination;

    public string $activeTab = 'history';
    public ?Pesanan $selectedOrder = null;
    public string $copiedCode = '';

    public function mount()
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('customer.login');
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function viewOrder(int $orderId): void
    {
        $customer = Auth::guard('customer')->user();
        $this->selectedOrder = Pesanan::with('details')
            ->where('customer_id', $customer->id)
            ->findOrFail($orderId);
        $this->dispatch('show-order-detail');
    }

    public function closeOrder(): void
    {
        $this->selectedOrder = null;
    }

    public function copyVoucher(string $code): void
    {
        $this->copiedCode = $code;
        $this->dispatch('copy-to-clipboard', code: $code);
    }

    public function logout(): void
    {
        Auth::guard('customer')->logout();
        session()->invalidate();
        session()->regenerateToken();
        redirect()->route('home');
    }

    public function render()
    {
        $customer = Auth::guard('customer')->user();

        $orders = Pesanan::with('details')
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate(10);

        $vouchers = Voucher::where('customer_id', $customer->id)
            ->orderByRaw('is_used ASC, ISNULL(expires_at) ASC, expires_at ASC, created_at DESC')
            ->get();

        $activeVouchers = $vouchers->filter(fn($v) => $v->isValid());
        $usedVouchers = $vouchers->filter(fn($v) => $v->is_used || $v->isExpired());

        return view('livewire.customer-account', [
            'customer' => $customer,
            'orders' => $orders,
            'activeVouchers' => $activeVouchers,
            'usedVouchers' => $usedVouchers,
            'web_settings' => WebSetting::current(),
        ])->layout('layouts.public', ['whiteBackground' => true]);
    }
}
