<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class Pesanan extends Model
{
    /** Cookie (terenkripsi) berisi ID pesanan milik pengunjung, agar riwayat tamu tidak hilang saat sesi habis. */
    public const VISITOR_COOKIE = 'pesanan_saya';

    public const STATUSES = ['menunggu_konfirmasi', 'diproses', 'diantar', 'dikirim', 'selesai', 'dibatalkan'];

    protected $table = 'pesanans';

    protected $fillable = [
        'nomor_pesanan',
        'user_id',
        'customer_id',
        'session_id',
        'nama_pemesan',
        'alamat_pengantaran',
        'waktu_pengantaran',
        'catatan',
        'total',
        'total_harga',
        'discount_code_id',
        'discount_amount',
        'voucher_id',
        'voucher_amount',
        'ongkir',
        'status',
        'order_type',
    ];

    protected $casts = [
        'total' => 'integer',
        'total_harga' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'ongkir' => 'decimal:2',
        'waktu_diproses' => 'datetime',
        'waktu_diantar' => 'datetime',
        'waktu_selesai' => 'datetime',
        'waktu_dibatalkan' => 'datetime',
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

    /**
     * Pesanan yang boleh dilihat pengunjung saat ini: dari sesi ini, dari cookie riwayat, atau milik customer yang login.
     */
    public function scopeOwnedByVisitor(Builder $query): Builder
    {
        $ids = static::visitorOrderIds();
        $customerId = Auth::guard('customer')->id();

        return $query->where(function (Builder $q) use ($ids, $customerId) {
            $q->where('session_id', session()->getId());

            if ($ids) {
                $q->orWhereIn('id', $ids);
            }
            if ($customerId) {
                $q->orWhere('customer_id', $customerId);
            }
        });
    }

    /**
     * @return array<int, int>
     */
    public static function visitorOrderIds(): array
    {
        $ids = json_decode((string) request()->cookie(static::VISITOR_COOKIE), true);

        return is_array($ids) ? array_values(array_filter($ids, 'is_int')) : [];
    }

    public static function rememberForVisitor(self $pesanan): void
    {
        $ids = array_slice(array_values(array_unique(array_merge([$pesanan->id], static::visitorOrderIds()))), 0, 30);

        Cookie::queue(static::VISITOR_COOKIE, json_encode($ids), 60 * 24 * 365);
    }

    public function whatsappMessage(): string
    {
        $this->loadMissing(['details', 'discountCode', 'voucher']);
        $rp = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');

        $message = "Assalamualaikum, saya ingin pesan:\n\n";
        foreach ($this->details as $item) {
            $name = $item->nama_minuman ?: $item->nama_makanan ?: $item->nama_botolan ?: '-';
            $options = array_filter([
                $item->nama_minuman && $item->size ? "Size: {$item->size}" : null,
                $item->gula ? "Gula: {$item->gula}" : null,
                $item->topping ? "Topping: {$item->topping}" : null,
            ]);
            $message .= "- {$name}" . ($options ? ' (' . implode(', ', $options) . ')' : '')
                . " x {$item->qty} = " . $rp($item->subtotal) . "\n";
            if ($item->catatan) {
                $message .= "  Catatan: {$item->catatan}\n";
            }
        }

        $subtotal = (float) $this->details->sum('subtotal');
        $message .= "\nSubtotal: " . $rp($subtotal);
        if ($this->discount_amount > 0) {
            $message .= "\nDiskon (" . ($this->discountCode?->code ?? 'kode promo') . '): -' . $rp($this->discount_amount);
        }
        if ($this->voucher_amount > 0) {
            $message .= "\nVoucher (" . ($this->voucher?->code ?? 'voucher') . '): -' . $rp($this->voucher_amount);
        }
        if ($this->ongkir > 0) {
            $message .= "\nOngkir: +" . $rp($this->ongkir);
        }
        $message .= "\nTotal: " . $rp($this->total);
        $message .= "\n\nNama: {$this->nama_pemesan}";
        $message .= "\nJenis Pesanan: " . ($this->order_type === 'delivery' ? 'Antar ke Alamat' : 'Ambil Sendiri');
        if ($this->order_type === 'delivery') {
            $message .= "\nAlamat: {$this->alamat_pengantaran}";
        }
        $message .= "\nWaktu " . ($this->order_type === 'delivery' ? 'Pengantaran' : 'Pengambilan') . ': ' . ($this->waktu_pengantaran ?: '-');
        $message .= "\nNomor Pesanan: {$this->nomor_pesanan}";

        return $message;
    }

    public function whatsappUrl(): ?string
    {
        $number = preg_replace('/[^0-9]/', '', (string) WebSetting::current()->whatsapp_number);

        return $number ? 'https://wa.me/' . $number . '?text=' . rawurlencode($this->whatsappMessage()) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }

    public function voucher()
    {
        return $this->belongsTo(\App\Models\Voucher::class);
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
