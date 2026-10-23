<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerManager extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCustomer = null;
    public $showDetail = false;
    public $orderSearch = '';

    // Form tambah/edit customer
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $nama = '';
    public string $no_hp = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'nama' => 'required|string|min:2|max:100',
            'no_hp' => ['required', 'string', 'min:9', 'max:15', Rule::unique('customers', 'no_hp')->ignore($this->editingId)],
            'password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:6', 'confirmed'],
        ];
    }

    protected $messages = [
        'nama.required' => 'Nama wajib diisi.',
        'nama.min' => 'Nama minimal 2 karakter.',
        'no_hp.required' => 'Nomor HP wajib diisi.',
        'no_hp.min' => 'Nomor HP tidak valid.',
        'no_hp.max' => 'Nomor HP tidak valid.',
        'no_hp.unique' => 'Nomor HP ini sudah terdaftar.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 6 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ];

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

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $customer = Customer::findOrFail($id);

        $this->resetForm();
        $this->editingId = $customer->id;
        $this->nama = $customer->nama;
        $this->no_hp = $customer->no_hp;
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function save(): void
    {
        // Simpan dengan format yang sama seperti registrasi agar customer tetap bisa login
        $this->no_hp = Customer::normalizePhone($this->no_hp);

        $this->validate();

        $data = [
            'nama' => trim($this->nama),
            'no_hp' => $this->no_hp,
        ];

        if ($this->password !== '') {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingId) {
            Customer::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Data customer berhasil diperbarui.');
        } else {
            Customer::create($data);
            session()->flash('success', 'Customer baru berhasil ditambahkan.');
        }

        $this->closeForm();
    }

    public function delete(int $id): void
    {
        $customer = Customer::findOrFail($id);

        // pesanans.customer_id di-set null (riwayat pesanan tetap ada), vouchers ikut terhapus
        $customer->delete();

        if ((int) $this->selectedCustomer === $id) {
            $this->closeDetail();
        }

        session()->flash('success', "Customer {$customer->nama} berhasil dihapus.");
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'nama', 'no_hp', 'password', 'password_confirmation']);
        $this->resetValidation();
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

                    // No HP disimpan 62xxx, jadi pencarian "08xx" juga dicocokkan dalam format itu
                    $searchPhone = preg_match('/^[0-9+\-\s]+$/', $this->search)
                        ? Customer::normalizePhone($this->search)
                        : '';

                    if ($searchPhone !== '') {
                        $sub->orWhere('no_hp', 'like', '%' . $searchPhone . '%');
                    }
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
