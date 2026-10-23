<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebSetting extends Model
{
    protected $fillable = [
        'site_name',
        'tagline',
        'logo_path',
        'favicon_path',
        'theme',
        'latitude',
        'longitude',
        'delivery_radius',
        'order_mode',
        'whatsapp_number',
        'opening_time',
        'closing_time',
        'is_temporarily_closed',
        'temporary_closure_message',
        'ongkir_enabled',
        'ongkir_per_km',
        'ongkir_free_km',
        'botolan_placeholder_path',
    ];

    protected $with = ['themeColor'];

    public const ORDER_MODES = ['both', 'delivery', 'takeaway'];

    public function themeColor()
    {
        return $this->belongsTo(ThemeColor::class, 'theme', 'name');
    }

    /**
     * Pengaturan toko; dibuat dengan nilai default bila belum ada agar halaman tidak error saat tabel kosong.
     */
    public static function current(): self
    {
        return static::firstOrCreate([], [
            'site_name' => 'Raihaan Coffee Corner',
            'theme' => 'green',
            'order_mode' => 'both',
        ]);
    }

    /**
     * Satu-satunya logika jam buka (dipakai middleware, halaman maintenance, dan checkout).
     */
    public function isOpenNow(): bool
    {
        return $this->closedReason() === null;
    }

    /**
     * @return 'temporarily_closed'|'outside_business_hours'|null
     */
    public function closedReason(): ?string
    {
        if ($this->is_temporarily_closed) {
            return 'temporarily_closed';
        }

        if (! $this->opening_time || ! $this->closing_time) {
            return null;
        }

        $now = now();
        $open = $now->copy()->setTimeFrom($this->opening_time);
        $close = $now->copy()->setTimeFrom($this->closing_time);

        // Jam buka lewat tengah malam, mis. 18:00–02:00
        $isOpen = $close->lessThanOrEqualTo($open)
            ? $now->greaterThanOrEqualTo($open) || $now->lessThan($close)
            : $now->between($open, $close);

        return $isOpen ? null : 'outside_business_hours';
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'opening_time' => 'datetime',
        'closing_time' => 'datetime',
        'is_temporarily_closed' => 'boolean',
        'ongkir_enabled' => 'boolean',
        'ongkir_per_km' => 'decimal:2',
        'ongkir_free_km' => 'decimal:2',
    ];
}
