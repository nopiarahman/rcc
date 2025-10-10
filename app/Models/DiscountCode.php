<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscountCode extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_amount',
        'minimum_purchase',
        'max_redeem',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'discount_amount' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
    ];

    /**
     * Get the usages for the discount code.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(DiscountCodeUsage::class);
    }

    /**
     * Get the orders that used this discount code.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

    /**
     * Check if the discount code is currently active based on date range and other conditions.
     */
    public function isActive(): bool
    {
        $now = now();
        
        // Check if active, within date range, and not exceeded max redeem
        $isActive = $this->is_active && 
                   $now->greaterThanOrEqualTo($this->start_date) && 
                   $now->lessThanOrEqualTo($this->end_date);
        
        // Check max redeem limit
        if ($this->max_redeem && $this->used_count >= $this->max_redeem) {
            $isActive = false;
        }
        
        return $isActive;
    }

    /**
     * Check if the discount code can be applied to the given total amount.
     */
    public function canBeApplied(float $totalAmount): bool
    {
        return $this->isActive() && $totalAmount >= $this->minimum_purchase;
    }

    /**
     * Calculate the discount amount for a given total.
     */
    public function calculateDiscount(float $totalAmount): float
    {
        if (!$this->canBeApplied($totalAmount)) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            $discountAmount = ($totalAmount * $this->discount_amount) / 100;
            return min($discountAmount, $totalAmount); // Don't discount more than the total
        } else { // fixed amount
            return min($this->discount_amount, $totalAmount); // Don't discount more than the total
        }
    }

    /**
     * Apply the discount code and increment usage count.
     */
    public function apply(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        // Check if we've reached max redeem
        if ($this->max_redeem && $this->used_count >= $this->max_redeem) {
            return false;
        }

        $this->increment('used_count');
        return true;
    }

    /**
     * Generate a unique discount code.
     */
    public static function generateUniqueCode(int $length = 8): string
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length));
        } while (self::where('code', $code)->exists());
        
        return $code;
    }

    /**
     * Format the discount amount for display.
     */
    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return (int)$this->discount_amount . '%';
        } else {
            return 'Rp ' . number_format($this->discount_amount, 0, ',', '.');
        }
    }

    /**
     * Get remaining uses count.
     */
    public function getRemainingUsesAttribute(): ?int
    {
        if ($this->max_redeem === null) {
            return null; // Unlimited
        }
        
        return max(0, $this->max_redeem - $this->used_count);
    }

    /**
     * Scope a query to only include active discount codes.
     */
    public function scopeActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    /**
     * Scope a query to only include discount codes that can be used.
     */
    public function scopeUsable($query, float $totalAmount = 0)
    {
        return $query->active()
                    ->where(function ($q) use ($totalAmount) {
                        $q->where('minimum_purchase', '<=', $totalAmount)
                          ->orWhere('minimum_purchase', 0);
                    })
                    ->where(function ($q) {
                        $q->whereNull('max_redeem')
                          ->orWhereRaw('used_count < max_redeem');
                    });
    }
}
