<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    protected $fillable = [
        'customer_id',
        'voucher_promo_rule_id',
        'code',
        'discount_type',
        'discount_value',
        'min_purchase',
        'description',
        'source_pesanan_id',
        'is_used',
        'used_at',
        'used_in_pesanan_id',
        'expires_at',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_purchase' => 'integer',
        'is_used' => 'boolean',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function promoRule(): BelongsTo
    {
        return $this->belongsTo(VoucherPromoRule::class, 'voucher_promo_rule_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return !$this->is_used && !$this->isExpired();
    }

    public function calculateDiscount(int|float $cartTotal): float
    {
        $cartTotal = max(0, (float) $cartTotal);

        // Potongan tidak pernah melebihi total belanja (juga untuk voucher persen)
        if ($this->discount_type === 'percentage') {
            return min(round($cartTotal * (min((float) $this->discount_value, 100) / 100)), $cartTotal);
        }
        return min((float) $this->discount_value, $cartTotal);
    }

    public function canBeApplied(int|float $cartTotal): bool
    {
        return $this->isValid() && $cartTotal >= $this->min_purchase;
    }

    public function markAsUsed(int $pesananId): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
            'used_in_pesanan_id' => $pesananId,
        ]);
    }

    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return (int)$this->discount_value . '%';
        }
        return 'Rp ' . number_format($this->discount_value, 0, ',', '.');
    }

    public static function generateCode(): string
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8));
        } while (static::where('code', $code)->exists());
        return $code;
    }

    public static function generateForOrder(\App\Models\Pesanan $pesanan): void
    {
        if (!$pesanan->customer_id) return;

        // Total kumulatif belanja (tanpa ongkir) semua order selesai milik customer ini
        $totalSpend = (int) \App\Models\Pesanan::where('customer_id', $pesanan->customer_id)
            ->where('status', 'selesai')
            ->sum(\Illuminate\Support\Facades\DB::raw('total - COALESCE(ongkir, 0)'));

        $rules = \App\Models\VoucherPromoRule::where('is_active', true)->get();

        foreach ($rules as $rule) {
            // Berapa voucher yang seharusnya sudah dimiliki berdasarkan total belanja
            $shouldHave = (int) floor($totalSpend / $rule->min_purchase_amount);

            // Berapa yang sudah pernah di-generate (termasuk yang sudah terpakai)
            $alreadyHave = static::where('customer_id', $pesanan->customer_id)
                ->where('voucher_promo_rule_id', $rule->id)
                ->count();

            // Generate selisihnya
            $toGenerate = $shouldHave - $alreadyHave;
            for ($i = 0; $i < $toGenerate; $i++) {
                static::create([
                    'customer_id'          => $pesanan->customer_id,
                    'voucher_promo_rule_id' => $rule->id,
                    'code'                 => static::generateCode(),
                    'discount_type'        => $rule->voucher_discount_type,
                    'discount_value'       => $rule->voucher_discount_value,
                    'min_purchase'         => $rule->voucher_min_purchase,
                    'description'          => $rule->description,
                    'source_pesanan_id'    => $pesanan->id,
                    'expires_at'           => now()->addDays($rule->voucher_validity_days),
                ]);
            }
        }
    }
}
