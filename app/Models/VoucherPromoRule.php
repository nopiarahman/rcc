<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherPromoRule extends Model
{
    protected $fillable = [
        'description',
        'min_purchase_amount',
        'voucher_discount_type',
        'voucher_discount_value',
        'voucher_min_purchase',
        'voucher_validity_days',
        'is_active',
    ];

    protected $casts = [
        'min_purchase_amount' => 'integer',
        'voucher_discount_value' => 'decimal:2',
        'voucher_min_purchase' => 'integer',
        'voucher_validity_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFormattedDiscountAttribute(): string
    {
        if ($this->voucher_discount_type === 'percentage') {
            return (int)$this->voucher_discount_value . '%';
        }
        return 'Rp ' . number_format($this->voucher_discount_value, 0, ',', '.');
    }

    public function getFormattedMinPurchaseAttribute(): string
    {
        return 'Rp ' . number_format($this->min_purchase_amount, 0, ',', '.');
    }
}
