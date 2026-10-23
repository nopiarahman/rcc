<?php

namespace App\Services;

use App\Helpers\DrinkPriceHelper;
use App\Models\BotolanProduk;
use App\Models\DiscountCode;
use App\Models\Makanan;
use App\Models\Minuman;
use App\Models\Voucher;
use Illuminate\Support\Collection;

/**
 * Satu-satunya tempat harga keranjang dihitung. Harga SELALU diambil dari database;
 * harga yang dikirim browser (localStorage / request) diabaikan.
 */
class CartService
{
    public const MAX_QTY = 50;
    public const MAX_NOTE_LENGTH = 200;
    public const TYPES = ['minuman', 'makanan', 'botolan'];

    /**
     * Ambil hanya field yang dikenal dengan tipe yang benar; item rusak dibuang dan item sama digabung.
     */
    public static function sanitize(array $raw): array
    {
        $clean = [];

        foreach ($raw as $item) {
            if (! is_array($item)) {
                continue;
            }

            $type = $item['type'] ?? 'minuman';
            $id = static::positiveInt($item['id'] ?? null);
            $qty = static::positiveInt($item['qty'] ?? 1);

            if (! in_array($type, static::TYPES, true) || ! $id || ! $qty) {
                continue;
            }

            $line = [
                'type' => $type,
                'id' => $id,
                'size_id' => $type === 'minuman' ? static::positiveInt($item['size_id'] ?? null) : null,
                'sugar_id' => $type === 'minuman' ? static::positiveInt($item['sugar_id'] ?? null) : null,
                'topping_id' => $type !== 'botolan' ? static::positiveInt($item['topping_id'] ?? null) : null,
                'ukuran_id' => $type === 'botolan' ? static::positiveInt($item['ukuran_id'] ?? null) : null,
                'qty' => min($qty, static::MAX_QTY),
                'catatan' => is_string($item['catatan'] ?? null)
                    ? mb_substr(trim($item['catatan']), 0, static::MAX_NOTE_LENGTH) ?: null
                    : null,
            ];

            $key = static::keyFor($line);
            if (isset($clean[$key])) {
                $clean[$key]['qty'] = min($clean[$key]['qty'] + $line['qty'], static::MAX_QTY);
            } else {
                $clean[$key] = $line;
            }
        }

        return $clean;
    }

    public static function keyFor(array $line): string
    {
        return implode('-', [
            $line['type'], $line['id'],
            $line['size_id'] ?? 0, $line['sugar_id'] ?? 0, $line['topping_id'] ?? 0, $line['ukuran_id'] ?? 0,
        ]);
    }

    /**
     * Tambahkan satu item ke keranjang (dipakai halaman detail produk).
     */
    public static function add(array $cart, array $line): array
    {
        return static::sanitize(array_merge(array_values($cart), [$line]));
    }

    /**
     * Hitung harga setiap item dari database. Item yang sudah tidak ada, habis, nonaktif,
     * atau opsinya tidak valid dikeluarkan dan dilaporkan di 'removed'.
     *
     * @return array{items: Collection, removed: array<int, string>}
     */
    public static function resolve(array $cart): array
    {
        $cart = static::sanitize($cart);
        $byType = collect($cart)->groupBy('type');

        $minumans = Minuman::with(['sizes', 'sugars', 'toppings', 'media'])
            ->whereIn('id', $byType->get('minuman', collect())->pluck('id'))->get()->keyBy('id');
        $makanans = Makanan::with(['toppings', 'media'])
            ->whereIn('id', $byType->get('makanan', collect())->pluck('id'))->get()->keyBy('id');
        $botolans = BotolanProduk::with(['minuman', 'ukurans', 'media'])
            ->whereIn('id', $byType->get('botolan', collect())->pluck('id'))->get()->keyBy('id');

        $items = collect();
        $removed = [];

        foreach ($cart as $key => $line) {
            $resolved = match ($line['type']) {
                'minuman' => static::resolveMinuman($line, $minumans->get($line['id'])),
                'makanan' => static::resolveMakanan($line, $makanans->get($line['id'])),
                'botolan' => static::resolveBotolan($line, $botolans->get($line['id'])),
            };

            if (is_string($resolved)) {
                $removed[$key] = $resolved;
                continue;
            }

            $items->put($key, $resolved + [
                'key' => $key,
                'type' => $line['type'],
                'id' => $line['id'],
                'qty' => $line['qty'],
                'catatan' => $line['catatan'],
                'subtotal' => $resolved['unit_price'] * $line['qty'],
            ]);
        }

        return compact('items', 'removed');
    }

    /**
     * Ringkasan total. Urutan: subtotal → kode diskon → voucher (dari sisa setelah diskon) → ongkir → pembulatan ke bawah per Rp1.000.
     */
    public static function summarize(Collection $items, ?DiscountCode $discountCode, ?Voucher $voucher, int $ongkir): array
    {
        $subtotal = (int) $items->sum('subtotal');
        $discount = $discountCode ? (int) round($discountCode->calculateDiscount($subtotal)) : 0;
        $voucherAmount = $voucher && $voucher->canBeApplied($subtotal)
            ? (int) round($voucher->calculateDiscount(max(0, $subtotal - $discount)))
            : 0;
        $ongkir = max(0, $ongkir);

        $beforeRounding = max(0, $subtotal - $discount - $voucherAmount) + $ongkir;
        $total = (int) (floor($beforeRounding / 1000) * 1000);

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'voucher' => $voucherAmount,
            'ongkir' => $ongkir,
            'rounding' => $total - $beforeRounding,
            'total' => $total,
            'qty' => (int) $items->sum('qty'),
        ];
    }

    protected static function resolveMinuman(array $line, ?Minuman $minuman): array|string
    {
        if (! $minuman) {
            return 'Produk sudah tidak tersedia.';
        }
        if ($minuman->is_habis) {
            return "{$minuman->nama} sedang habis.";
        }

        $options = DrinkPriceHelper::resolveOptions($minuman, $line['size_id'], $line['sugar_id'], $line['topping_id']);
        if (! $options['valid']) {
            return "Pilihan varian {$minuman->nama} sudah tidak tersedia.";
        }

        $regular = (int) round(DrinkPriceHelper::calculate($minuman, $line['size_id'], $line['sugar_id'], $line['topping_id']));

        return static::withProductDiscount($minuman, $regular) + [
            'name' => $minuman->nama,
            'model' => $minuman,
            'image' => $minuman->getFirstMediaUrl('foto') ?: null,
            'size' => $options['size']?->name,
            'sugar' => $options['sugar']?->level,
            'topping' => $options['topping']?->nama,
            'ukuran' => null,
        ];
    }

    protected static function resolveMakanan(array $line, ?Makanan $makanan): array|string
    {
        if (! $makanan) {
            return 'Produk sudah tidak tersedia.';
        }
        if ($makanan->is_habis) {
            return "{$makanan->nama} sedang habis.";
        }

        $topping = $makanan->toppings->firstWhere('id', $line['topping_id'] ?? $makanan->default_topping);
        if ($line['topping_id'] !== null && ! $topping) {
            return "Pilihan topping {$makanan->nama} sudah tidak tersedia.";
        }

        $regular = (int) round($makanan->base_price + ($topping?->default_price ?? 0));

        return static::withProductDiscount($makanan, $regular) + [
            'name' => $makanan->nama,
            'model' => $makanan,
            'image' => $makanan->getFirstMediaUrl('gambar') ?: null,
            'size' => null,
            'sugar' => null,
            'topping' => $topping?->nama,
            'ukuran' => null,
        ];
    }

    protected static function resolveBotolan(array $line, ?BotolanProduk $produk): array|string
    {
        if (! $produk || ! $produk->is_active || ! $produk->minuman) {
            return 'Produk botolan sudah tidak tersedia.';
        }
        if ($produk->minuman->is_habis) {
            return "{$produk->minuman->nama} botolan sedang habis.";
        }

        $ukuran = $produk->ukurans->firstWhere('id', $line['ukuran_id']);
        if (! $ukuran || ! $ukuran->is_active) {
            return "Ukuran botolan {$produk->minuman->nama} sudah tidak tersedia.";
        }

        return [
            'name' => $produk->minuman->nama,
            'model' => $produk,
            'image' => $produk->getFirstMediaUrl('foto') ?: null,
            'unit_price' => (int) $ukuran->harga,
            'regular_price' => (int) $ukuran->harga,
            'discount_info' => null,
            'size' => null,
            'sugar' => null,
            'topping' => null,
            'ukuran' => $ukuran->label,
        ];
    }

    protected static function withProductDiscount(Minuman|Makanan $product, int $regular): array
    {
        $discount = $product->activeDiscount();

        if (! $discount) {
            return ['unit_price' => $regular, 'regular_price' => $regular, 'discount_info' => null];
        }

        return [
            'unit_price' => (int) round($discount->calculateDiscountedPrice($regular)),
            'regular_price' => $regular,
            'discount_info' => [
                'name' => $discount->name,
                'discount_text' => $discount->discount_type === 'percentage'
                    ? (int) $discount->discount_amount . '%'
                    : 'Rp' . number_format($discount->discount_amount, 0, ',', '.'),
            ],
        ];
    }

    protected static function positiveInt(mixed $value): ?int
    {
        $int = filter_var($value, FILTER_VALIDATE_INT);

        return $int !== false && $int > 0 ? $int : null;
    }
}
