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

    /**
     * Normalize a phone number to the stored 62xxx format (same as customer register/login).
     */
    public static function normalizePhone(string $noHp): string
    {
        $noHp = preg_replace('/[^0-9]/', '', $noHp);

        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        }

        return $noHp;
    }

    /**
     * Nomor HP untuk ditampilkan, mis. 6282375207570 → 0823-7520-7570.
     */
    public function getFormattedPhoneAttribute(): string
    {
        $local = str_starts_with((string) $this->no_hp, '62') ? '0' . substr($this->no_hp, 2) : (string) $this->no_hp;

        return preg_replace('/^(\d{4})(\d{4})(\d+)$/', '$1-$2-$3', $local);
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->nama))
            ->take(2)
            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
            ->implode('');
    }
}
