<?php

namespace App\Livewire\CustomerAuth;

use Livewire\Component;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Register extends Component
{
    public string $nama = '';
    public string $no_hp = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'nama' => 'required|string|min:2|max:100',
            'no_hp' => 'required|string|min:9|max:15|unique:customers,no_hp',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    protected $messages = [
        'nama.required' => 'Nama wajib diisi.',
        'nama.min' => 'Nama minimal 2 karakter.',
        'no_hp.required' => 'Nomor HP wajib diisi.',
        'no_hp.min' => 'Nomor HP tidak valid.',
        'no_hp.unique' => 'Nomor HP ini sudah terdaftar.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 6 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ];

    public function register()
    {
        $this->validate();

        $noHp = preg_replace('/[^0-9]/', '', $this->no_hp);
        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        }

        $customer = Customer::create([
            'nama' => trim($this->nama),
            'no_hp' => $noHp,
            'password' => Hash::make($this->password),
        ]);

        Auth::guard('customer')->login($customer, true);

        // Link session orders to new customer account
        $sessionId = session()->getId();
        \App\Models\Pesanan::where('session_id', $sessionId)
            ->whereNull('customer_id')
            ->update(['customer_id' => $customer->id]);

        return redirect()->route('customer.account');
    }

    public function render()
    {
        return view('livewire.customer-auth.register')
            ->layout('layouts.public');
    }
}
