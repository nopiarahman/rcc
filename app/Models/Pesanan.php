<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $table = 'pesanans';
    
    protected $fillable = [
        'nomor_pesanan',
        'user_id',
        'session_id',
        'nama_pemesan',
        'alamat_pengantaran',
        'waktu_pengantaran',
        'catatan',
        'total',
        'total_harga',
        'discount_code_id',
        'discount_amount',
        'status',
        'order_type',
    ];

    protected $casts = [
        'total' => 'integer',
        'total_harga' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pesanan) {
            if (empty($pesanan->nomor_pesanan)) {
                $pesanan->nomor_pesanan = 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
            }
            
            if (empty($pesanan->status)) {
                $pesanan->status = 'menunggu_konfirmasi';
            }
        });
    }

    public function details(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'pesanan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the discount code used for this order.
     */
    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

    /**
     * Get the discount usage record for this order.
     */
    public function discountUsage()
    {
        return $this->hasOne(DiscountCodeUsage::class);
    }

    /**
     * Get the total before discount.
     */
    public function getOriginalTotalAttribute(): float
    {
        return $this->total + $this->discount_amount;
    }

    /**
     * Get the formatted original total.
     */
    public function getFormattedOriginalTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->original_total, 0, ',', '.');
    }

    /**
     * Get the formatted discount amount.
     */
    public function getFormattedDiscountAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->discount_amount, 0, ',', '.');
    }

    /**
     * Check if this order has a discount applied.
     */
    public function hasDiscount(): bool
    {
        return $this->discount_amount > 0 && $this->discountCode !== null;
    }
}
