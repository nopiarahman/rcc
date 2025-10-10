<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'minuman_id',
        'makanan_id',
        'name',
        'description',
        'discount_amount',
        'discount_type',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'discount_amount' => 'decimal:2',
    ];

    /**
     * Get the minuman that owns the discount.
     */
    public function minuman(): BelongsTo
    {
        return $this->belongsTo(Minuman::class);
    }

    /**
     * Get the makanan that owns the discount.
     */
    public function makanan(): BelongsTo
    {
        return $this->belongsTo(Makanan::class);
    }

    /**
     * Get the product (minuman or makanan) that owns the discount.
     */
    public function product()
    {
        if ($this->minuman_id) {
            return $this->minuman();
        } elseif ($this->makanan_id) {
            return $this->makanan();
        }
        
        return null;
    }

    /**
     * Get the product name.
     */
    public function getProductNameAttribute(): string
    {
        if ($this->minuman) {
            return $this->minuman->nama;
        } elseif ($this->makanan) {
            return $this->makanan->nama;
        }
        
        return 'Unknown Product';
    }

    /**
     * Check if the discount is currently active based on date range.
     */
    public function isActive(): bool
    {
        $now = now();
        return $this->is_active && 
               $now->greaterThanOrEqualTo($this->start_date) && 
               $now->lessThanOrEqualTo($this->end_date);
    }

    /**
     * Calculate the discounted price for a given price.
     */
    public function calculateDiscountedPrice(float $originalPrice): float
    {
        if ($this->discount_type === 'percentage') {
            $discountAmount = ($originalPrice * $this->discount_amount) / 100;
            return max(0, $originalPrice - $discountAmount);
        } else { // fixed amount
            return max(0, $originalPrice - $this->discount_amount);
        }
    }
}
