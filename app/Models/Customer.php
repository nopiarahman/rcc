<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Authenticatable
{
    protected $fillable = ['nama', 'no_hp', 'password'];

    protected $hidden = ['password', 'remember_token'];

    public function pesanans(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'customer_id');
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'customer_id');
    }

    public function activeVouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'customer_id')
            ->where('is_used', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->nama))
            ->take(2)
            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
            ->implode('');
    }
}
