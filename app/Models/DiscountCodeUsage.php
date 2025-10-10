<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountCodeUsage extends Model
{
    protected $fillable = [
        'discount_code_id',
        'pesanan_id',
        'discount_amount',
        'original_total',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'original_total' => 'decimal:2',
    ];

    /**
     * Get the discount code that owns the usage.
     */
    public function discountCode(): BelongsTo
    {
        return $this->belongsTo(DiscountCode::class);
    }

    /**
     * Get the order that owns the usage.
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    /**
     * Get the formatted discount amount.
     */
    public function getFormattedDiscountAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->discount_amount, 0, ',', '.');
    }

    /**
     * Get the formatted original total.
     */
    public function getFormattedOriginalTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->original_total, 0, ',', '.');
    }

    /**
     * Get the final total after discount.
     */
    public function getFinalTotalAttribute(): float
    {
        return $this->original_total - $this->discount_amount;
    }

    /**
     * Get the formatted final total.
     */
    public function getFormattedFinalTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->final_total, 0, ',', '.');
    }
}
