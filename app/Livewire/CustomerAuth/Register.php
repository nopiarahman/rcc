<?php

namespace App\Livewire\CustomerAuth;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

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
        'no_hp.max' => 'Nomor HP tidak valid.',
        'no_hp.unique' => 'Nomor HP ini sudah terdaftar.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 6 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ];

    public function register()
    {
        // Batasi pendaftaran per IP agar tidak dibanjiri akun palsu
        $throttleKey = 'customer-register:' . request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $this->addError('no_hp', 'Terlalu banyak pendaftaran dari perangkat ini. Coba lagi dalam ' . ceil(RateLimiter::availableIn($throttleKey) / 60) . ' menit.');
            return null;
        }

        // Normalisasi dulu (08xx → 628xx) supaya pengecekan nomor terdaftar memakai format yang tersimpan
        $this->no_hp = Customer::normalizePhone($this->no_hp);

        $this->validate();

        RateLimiter::hit($throttleKey, 600);

        $customer = Customer::create([
            'nama' => trim($this->nama),
            'no_hp' => $this->no_hp,
            'password' => Hash::make($this->password),
        ]);

        Auth::guard('customer')->login($customer, true);

        // Hubungkan pesanan tamu (sesi ini & cookie riwayat) ke akun baru
        Pesanan::whereNull('customer_id')
            ->where(fn ($q) => $q->where('session_id', session()->getId())->orWhereIn('id', Pesanan::visitorOrderIds()))
            ->update(['customer_id' => $customer->id]);

        session()->regenerate();

        return redirect()->route('customer.account');
    }

    public function render()
    {
        return view('livewire.customer-auth.register')
            ->layout('layouts.public')->title('Daftar Akun');
    }
}
