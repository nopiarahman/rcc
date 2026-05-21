<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class CustomerManager extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCustomer = null;
    public $showDetail = false;
    public $orderSearch = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function viewDetail(int $id): void
    {
        $this->selectedCustomer = $id;
        $this->orderSearch = '';
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->selectedCustomer = null;
        $this->showDetail = false;
    }

    public function render()
    {
        $totalCustomers = Customer::count();
        $totalOrdersFromCustomers = Pesanan::whereNotNull('customer_id')->count();
        $totalRevenueFromCustomers = Pesanan::whereNotNull('customer_id')
            ->where('status', '!=', 'dibatalkan')
            ->sum('total_harga');

        $customers = Customer::withCount('pesanans')
            ->withSum(['pesanans as total_belanja' => function ($q) {
                $q->where('status', '!=', 'dibatalkan');
            }], 'total_harga')
            ->when($this->search, fn ($q) =>
                $q->where(function ($sub) {
                    $sub->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('no_hp', 'like', '%' . $this->search . '%');
                })
            )
            ->orderByDesc('created_at')
            ->paginate(15);

        $customer = null;
        $orders = null;

        if ($this->showDetail && $this->selectedCustomer) {
            $customer = Customer::find($this->selectedCustomer);

            if ($customer) {
                $orders = Pesanan::with('details')
                    ->where('customer_id', $customer->id)
                    ->when($this->orderSearch, fn ($q) =>
                        $q->where('nomor_pesanan', 'like', '%' . $this->orderSearch . '%')
                    )
                    ->orderByDesc('created_at')
                    ->paginate(10, pageName: 'orderPage');
            }
        }

        return view('livewire.admin.customer-manager', compact(
            'customers',
            'totalCustomers',
            'totalOrdersFromCustomers',
            'totalRevenueFromCustomers',
            'customer',
            'orders',
        ))->layout('components.layouts.app');
    }
}
