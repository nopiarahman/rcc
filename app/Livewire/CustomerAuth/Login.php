<?php

namespace App\Livewire\CustomerAuth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

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
            'no_hp' => 'required|string',
            'password' => 'required|string',
        ]);

        $noHp = preg_replace('/[^0-9]/', '', $this->no_hp);
        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        }

        if (Auth::guard('customer')->attempt(['no_hp' => $noHp, 'password' => $this->password], $this->remember)) {
            $customer = Auth::guard('customer')->user();

            // Link session orders to this customer account
            $sessionId = session()->getId();
            \App\Models\Pesanan::where('session_id', $sessionId)
                ->whereNull('customer_id')
                ->update(['customer_id' => $customer->id]);

            session()->regenerate();
            return redirect()->route('customer.account');
        }

        $this->addError('no_hp', 'Nomor HP atau password salah.');
    }

    public function render()
    {
        return view('livewire.customer-auth.login')
            ->layout('layouts.public');
    }
}
