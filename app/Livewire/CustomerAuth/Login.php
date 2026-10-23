<?php

namespace App\Livewire\CustomerAuth;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class Login extends Component
{
    public string $no_hp = '';
    public string $password = '';
    public bool $remember = false;

    protected $messages = [
        'no_hp.required' => 'Nomor HP wajib diisi.',
        'password.required' => 'Password wajib diisi.',
    ];

    public function login()
    {
        $this->validate([
            'no_hp' => 'required|string|max:20',
            'password' => 'required|string',
        ]);

        $noHp = Customer::normalizePhone($this->no_hp);

        // Maksimal 5 percobaan per menit per nomor HP + IP, sama seperti login admin
        $throttleKey = 'customer-login:' . $noHp . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $this->addError('no_hp', 'Terlalu banyak percobaan masuk. Coba lagi dalam ' . RateLimiter::availableIn($throttleKey) . ' detik.');
            return;
        }

        if (Auth::guard('customer')->attempt(['no_hp' => $noHp, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($throttleKey);
            $customer = Auth::guard('customer')->user();

            // Hubungkan pesanan tamu (sesi ini & cookie riwayat) ke akun customer
            Pesanan::whereNull('customer_id')
                ->where(fn ($q) => $q->where('session_id', session()->getId())->orWhereIn('id', Pesanan::visitorOrderIds()))
                ->update(['customer_id' => $customer->id]);

            session()->regenerate();
            return redirect()->route('customer.account');
        }

        RateLimiter::hit($throttleKey);
        $this->addError('no_hp', 'Nomor HP atau password salah.');
    }

    public function render()
    {
        return view('livewire.customer-auth.login')
            ->layout('layouts.public')->title('Masuk');
    }
}
