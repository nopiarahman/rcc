<?php

namespace App\Livewire;

use App\Models\DiscountCode;
use App\Models\DiscountCodeUsage;
use App\Models\Pesanan;
use App\Models\Voucher;
use App\Models\WebSetting;
use App\Services\CartService;
use App\Services\OsrmService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class CartPage extends Component
{
    // Properti #[Locked] hanya boleh diubah oleh server, tidak bisa dimanipulasi dari browser
    #[Locked]
    public array $cart = [];

    #[Locked]
    public ?string $order_type = 'delivery';

    #[Locked]
    public ?int $applied_discount_id = null;

    #[Locked]
    public ?int $applied_voucher_id = null;

    #[Locked]
    public ?float $customer_lat = null;

    #[Locked]
    public ?float $customer_lng = null;

    #[Locked]
    public int $ongkir = 0;

    #[Locked]
    public float $ongkir_distance_km = 0;

    #[Locked]
    public string $distance_method = '';

    #[Locked]
    public array $removed_notices = [];

    public $nama_pemesan = '';
    public $alamat_pengantaran = '';
    public $waktu_pengantaran = '';
    public $catatan = '';
    public $discount_code = '';
    public $discount_error = '';
    public $voucher_error = '';
    public $location_error = '';

    public function mount()
    {
        $this->cart = CartService::sanitize(session('cart', []));
        $this->persistCart(false);

        $mode = $this->settings()->order_mode;
        if (in_array($mode, ['takeaway', 'delivery'], true)) {
            $this->order_type = $mode;
        }

        if (Auth::guard('customer')->check() && empty($this->nama_pemesan)) {
            $this->nama_pemesan = Auth::guard('customer')->user()->nama;
        }

        $this->autoApplyBestVoucher();
    }

    protected function settings(): WebSetting
    {
        return WebSetting::current();
    }

    /**
     * Simpan keranjang ke session dan beri tahu komponen/Local Storage.
     */
    protected function persistCart(bool $notify = true): void
    {
        session()->put('cart', $this->cart);

        if ($notify) {
            $this->dispatch('cartUpdated');
            $this->dispatch('updateLocalCart', cart: array_values($this->cart));
        }
    }

    protected function resolvedCart(): array
    {
        $resolved = CartService::resolve($this->cart);

        // Item yang sudah tidak tersedia dikeluarkan dari keranjang dan diberitahukan ke pembeli
        if ($resolved['removed']) {
            $this->removed_notices = array_values(array_unique(array_merge($this->removed_notices, $resolved['removed'])));
            $this->cart = array_diff_key($this->cart, $resolved['removed']);
            $this->persistCart();
        }

        return $resolved;
    }

    protected function appliedDiscount(): ?DiscountCode
    {
        return $this->applied_discount_id ? DiscountCode::find($this->applied_discount_id) : null;
    }

    /**
     * Voucher hanya berlaku jika milik customer yang login dan masih valid.
     */
    protected function appliedVoucher(): ?Voucher
    {
        $customerId = Auth::guard('customer')->id();
        if (! $this->applied_voucher_id || ! $customerId) {
            return null;
        }

        $voucher = Voucher::where('id', $this->applied_voucher_id)->where('customer_id', $customerId)->first();

        return $voucher && $voucher->isValid() ? $voucher : null;
    }

    protected function ongkirAmount(): int
    {
        return $this->order_type === 'delivery' ? $this->ongkir : 0;
    }

    protected function autoApplyBestVoucher(): void
    {
        $customer = Auth::guard('customer')->user();
        if (! $customer || $this->applied_voucher_id) {
            return;
        }

        $subtotal = (int) CartService::resolve($this->cart)['items']->sum('subtotal');
        if ($subtotal <= 0) {
            return;
        }

        $best = Voucher::where('customer_id', $customer->id)
            ->where('is_used', false)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->where('min_purchase', '<=', $subtotal)
            ->get()
            ->sortByDesc(fn ($v) => $v->calculateDiscount($subtotal))
            ->first();

        $this->applied_voucher_id = $best?->id;
    }

    public function applyVoucher(int $voucherId): void
    {
        $this->voucher_error = '';
        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            return;
        }

        $voucher = Voucher::where('id', $voucherId)->where('customer_id', $customer->id)->first();
        if (! $voucher || ! $voucher->isValid()) {
            $this->voucher_error = 'Voucher tidak valid.';
            return;
        }

        $subtotal = (int) CartService::resolve($this->cart)['items']->sum('subtotal');
        if ($subtotal < $voucher->min_purchase) {
            $this->voucher_error = 'Min. belanja Rp ' . number_format($voucher->min_purchase, 0, ',', '.') . ' untuk voucher ini.';
            return;
        }

        $this->applied_voucher_id = $voucher->id;
    }

    public function clearVoucher(): void
    {
        $this->applied_voucher_id = null;
        $this->voucher_error = '';
    }

    public function increaseQty(string $key): void
    {
        if (! isset($this->cart[$key])) {
            return;
        }

        $this->cart[$key]['qty'] = min($this->cart[$key]['qty'] + 1, CartService::MAX_QTY);
        $this->persistCart();
    }

    public function decreaseQty(string $key): void
    {
        if (! isset($this->cart[$key]) || $this->cart[$key]['qty'] <= 1) {
            return;
        }

        $this->cart[$key]['qty']--;
        $this->persistCart();
    }

    public function removeItem(string $key): void
    {
        unset($this->cart[$key]);
        $this->persistCart();
    }

    public function clearCart(): void
    {
        $this->cart = [];
        session()->forget('cart');
        $this->dispatch('cartUpdated');
        $this->dispatch('clearLocalCart');
        $this->clearDiscountCode();
        $this->clearVoucher();
    }

    public function dismissRemovedNotices(): void
    {
        $this->removed_notices = [];
    }

    public function applyDiscountCode(): void
    {
        $this->discount_error = '';

        if (empty(trim((string) $this->discount_code))) {
            $this->discount_error = 'Masukkan kode diskon terlebih dahulu.';
            return;
        }

        $discountCode = DiscountCode::where('code', strtoupper(trim($this->discount_code)))->first();
        if (! $discountCode) {
            $this->discount_error = 'Kode diskon tidak valid.';
            return;
        }

        $subtotal = (int) CartService::resolve($this->cart)['items']->sum('subtotal');
        $error = $this->discountCodeError($discountCode, $subtotal);
        if ($error) {
            $this->discount_error = $error;
            return;
        }

        $this->applied_discount_id = $discountCode->id;
        $this->dispatch('discount-applied');
    }

    protected function discountCodeError(DiscountCode $discountCode, int $subtotal): ?string
    {
        if (! $discountCode->isActive()) {
            return 'Kode diskon sudah tidak aktif atau telah kadaluarsa.';
        }
        if ($subtotal < $discountCode->minimum_purchase) {
            return 'Minimum pembelian Rp ' . number_format($discountCode->minimum_purchase, 0, ',', '.') . ' untuk menggunakan kode ini.';
        }
        $customerId = Auth::guard('customer')->id();
        if (! $discountCode->canBeUsedByCustomer($customerId)) {
            return $customerId
                ? 'Anda sudah mencapai batas pemakaian kode ini.'
                : 'Kode ini khusus pelanggan terdaftar. Silakan masuk ke akun Anda terlebih dahulu.';
        }

        return null;
    }

    public function clearDiscountCode(): void
    {
        $this->discount_code = '';
        $this->applied_discount_id = null;
        $this->discount_error = '';
    }

    public function updatedDiscountCode(): void
    {
        $this->discount_error = '';

        $applied = $this->appliedDiscount();
        if ($applied && strtoupper((string) $this->discount_code) !== $applied->code) {
            $this->applied_discount_id = null;
        }
    }

    public function selectOrderType(string $type): void
    {
        $mode = $this->settings()->order_mode;
        if (! in_array($type, ['delivery', 'takeaway'], true) || ($mode !== 'both' && $mode !== $type)) {
            return;
        }

        $this->order_type = $type;
        $this->location_error = '';
        $this->resetValidation();
        $this->resetLocation();
    }

    protected function resetLocation(): void
    {
        $this->customer_lat = null;
        $this->customer_lng = null;
        $this->ongkir = 0;
        $this->ongkir_distance_km = 0;
        $this->distance_method = '';
    }

    public function setCustomerLocation(float $lat, float $lng): void
    {
        $this->location_error = '';
        $this->resetErrorBag('lokasi');
        $this->resetLocation();

        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            $this->location_error = 'Lokasi tidak valid.';
            return;
        }

        $error = $this->radiusError($lat, $lng);
        if ($error) {
            $this->location_error = $error;
            return;
        }

        $this->customer_lat = $lat;
        $this->customer_lng = $lng;
        $this->calculateOngkir();
    }

    public function setLocationFailed(string $message): void
    {
        $this->resetLocation();
        $this->location_error = mb_substr($message, 0, 150);
    }

    /**
     * Radius pengantaran dicek di server (garis lurus, dalam meter, sesuai pengaturan Web Settings).
     */
    protected function radiusError(float $lat, float $lng): ?string
    {
        $settings = $this->settings();
        $radius = (int) $settings->delivery_radius;
        if ($radius <= 0 || $settings->latitude === null || $settings->longitude === null) {
            return null;
        }

        $distanceKm = OsrmService::haversineKm((float) $settings->latitude, (float) $settings->longitude, $lat, $lng);
        if ($distanceKm * 1000 <= $radius) {
            return null;
        }

        return 'Lokasi Anda sekitar ' . number_format($distanceKm, 1, ',', '.') . ' km dari toko, di luar jangkauan pengantaran (maks. '
            . number_format($radius / 1000, 1, ',', '.') . ' km). Silakan pilih Ambil Sendiri.';
    }

    protected function calculateOngkir(): void
    {
        if ($this->customer_lat === null) {
            return;
        }

        $settings = $this->settings();
        $osrm = new OsrmService();
        $result = $osrm->getDistance((float) $settings->latitude, (float) $settings->longitude, $this->customer_lat, $this->customer_lng);

        $this->ongkir_distance_km = $result['km'];
        $this->distance_method = $result['method'];

        $this->ongkir = $settings->ongkir_enabled && $this->order_type === 'delivery'
            ? $osrm->calculateOngkir($this->ongkir_distance_km, (float) $settings->ongkir_per_km, (float) $settings->ongkir_free_km)
            : 0;
    }

    /**
     * Langkah 1: cek kelengkapan sebelum membuka modal konfirmasi.
     */
    public function checkout(): void
    {
        $this->resetValidation();

        if (! $this->guardCheckoutPreconditions()) {
            return;
        }

        $this->dispatch('show-checkout-modal');
    }

    protected function guardCheckoutPreconditions(): bool
    {
        if (! $this->settings()->isOpenNow()) {
            $this->addError('checkout', 'Maaf, toko sedang tutup. Pesanan belum bisa dibuat.');
            return false;
        }
        if (empty($this->cart)) {
            $this->addError('checkout', 'Keranjang belanja kosong.');
            return false;
        }
        if (! in_array($this->order_type, ['delivery', 'takeaway'], true)) {
            $this->addError('order_type', 'Pilih jenis pesanan terlebih dahulu.');
            return false;
        }
        if ($this->order_type === 'delivery') {
            if (empty(trim((string) $this->alamat_pengantaran))) {
                $this->addError('alamat_pengantaran', 'Isi alamat pengantaran terlebih dahulu.');
                return false;
            }
            if ($this->customer_lat === null || $this->customer_lng === null) {
                $this->addError('lokasi', 'Tentukan lokasi pengantaran dengan tombol "Gunakan Lokasi Saya" agar ongkir bisa dihitung.');
                return false;
            }
            $radiusError = $this->radiusError($this->customer_lat, $this->customer_lng);
            if ($radiusError) {
                $this->addError('lokasi', $radiusError);
                return false;
            }
        }

        return true;
    }

    public function render()
    {
        $resolved = $this->resolvedCart();
        $discount = $this->appliedDiscount();
        $voucher = $this->appliedVoucher();
        $summary = CartService::summarize($resolved['items'], $discount, $voucher, $this->ongkirAmount());
        $settings = $this->settings();
        $customer = Auth::guard('customer')->user();

        return view('livewire.cart-page', [
            'cartItems' => $resolved['items'],
            'summary' => $summary,
            'applied_discount' => $discount,
            'applied_voucher' => $summary['voucher'] > 0 ? $voucher : null,
            'orderMode' => $settings->order_mode,
            'web_settings' => $settings,
            'customer' => $customer,
            'customerVouchers' => $customer
                ? Voucher::where('customer_id', $customer->id)->where('is_used', false)
                    ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                    ->get()
                : collect(),
        ])->layout('layouts.public')->title('Keranjang');
    }

    /**
     * Langkah 2: buat pesanan. Semua harga dihitung ulang dari database dan kode diskon/voucher
     * dikunci di dalam transaksi agar tidak bisa dipakai melebihi batas.
     */
    public function konfirmasiCheckout()
    {
        $this->validate([
            'nama_pemesan' => 'required|string|max:100',
            'waktu_pengantaran' => ['required', 'string', 'max:20', 'regex:/^\d{2}\.\d{2} WIB$/'],
            'alamat_pengantaran' => ($this->order_type === 'delivery' ? 'required' : 'nullable') . '|string|max:500',
            'catatan' => 'nullable|string|max:500',
        ], [
            'nama_pemesan.required' => 'Nama pemesan wajib diisi.',
            'nama_pemesan.max' => 'Nama pemesan maksimal 100 karakter.',
            'waktu_pengantaran.required' => 'Pilih waktu terlebih dahulu.',
            'waktu_pengantaran.regex' => 'Pilih waktu dari daftar yang tersedia.',
            'alamat_pengantaran.required' => 'Isi alamat pengantaran terlebih dahulu.',
            'alamat_pengantaran.max' => 'Alamat maksimal 500 karakter.',
        ]);

        if (! $this->guardCheckoutPreconditions()) {
            return null;
        }

        $resolved = CartService::resolve($this->cart);
        if ($resolved['removed']) {
            $this->addError('checkout', 'Ada item yang sudah tidak tersedia dan telah dikeluarkan dari keranjang. Silakan periksa kembali.');
            $this->resolvedCart();
            return null;
        }

        $customer = Auth::guard('customer')->user();

        try {
            $pesanan = DB::transaction(function () use ($resolved, $customer) {
                $discount = null;
                if ($this->applied_discount_id) {
                    $discount = DiscountCode::lockForUpdate()->find($this->applied_discount_id);
                    $subtotal = (int) $resolved['items']->sum('subtotal');
                    $error = $discount ? $this->discountCodeError($discount, $subtotal) : 'Kode diskon tidak valid.';
                    if ($error) {
                        $this->applied_discount_id = null;
                        throw ValidationException::withMessages(['checkout' => $error . ' Kode diskon dilepas, silakan periksa total kembali.']);
                    }
                }

                $voucher = null;
                if ($this->applied_voucher_id) {
                    $voucher = Voucher::lockForUpdate()->find($this->applied_voucher_id);
                    if (! $voucher || ! $customer || (int) $voucher->customer_id !== (int) $customer->id || ! $voucher->isValid()) {
                        $this->applied_voucher_id = null;
                        throw ValidationException::withMessages(['checkout' => 'Voucher sudah tidak berlaku dan telah dilepas. Silakan periksa total kembali.']);
                    }
                }

                $summary = CartService::summarize($resolved['items'], $discount, $voucher, $this->ongkirAmount());

                $pesanan = Pesanan::create([
                    'user_id' => Auth::guard('web')->id(),
                    'customer_id' => $customer?->id,
                    'session_id' => session()->getId(),
                    'nama_pemesan' => trim($this->nama_pemesan),
                    'alamat_pengantaran' => $this->order_type === 'delivery' ? trim($this->alamat_pengantaran) : 'Takeaway',
                    'waktu_pengantaran' => $this->waktu_pengantaran,
                    'catatan' => $this->catatan ?: null,
                    'total' => $summary['total'],
                    'total_harga' => $summary['total'],
                    'ongkir' => $summary['ongkir'],
                    'discount_code_id' => $summary['discount'] > 0 ? $discount->id : null,
                    'discount_amount' => $summary['discount'],
                    'voucher_id' => $summary['voucher'] > 0 ? $voucher->id : null,
                    'voucher_amount' => $summary['voucher'],
                    'status' => 'diproses',
                    'order_type' => $this->order_type,
                ]);

                foreach ($resolved['items'] as $item) {
                    $pesanan->details()->create([
                        'minuman_id' => $item['type'] === 'minuman' ? $item['id'] : null,
                        'nama_minuman' => $item['type'] === 'minuman' ? $item['name'] : null,
                        'makanan_id' => $item['type'] === 'makanan' ? $item['id'] : null,
                        'nama_makanan' => $item['type'] === 'makanan' ? $item['name'] : null,
                        'botolan_produk_id' => $item['type'] === 'botolan' ? $item['id'] : null,
                        'botolan_ukuran_id' => $item['type'] === 'botolan' ? $this->cart[$item['key']]['ukuran_id'] : null,
                        'nama_botolan' => $item['type'] === 'botolan' ? $item['name'] . ' - ' . $item['ukuran'] : null,
                        'size' => $item['type'] === 'botolan' ? $item['ukuran'] : $item['size'],
                        'gula' => $item['sugar'],
                        'topping' => $item['topping'],
                        'catatan' => $item['catatan'],
                        'harga' => $item['unit_price'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }

                if ($summary['discount'] > 0) {
                    $discount->increment('used_count');
                    DiscountCodeUsage::create([
                        'discount_code_id' => $discount->id,
                        'pesanan_id' => $pesanan->id,
                        'discount_amount' => $summary['discount'],
                        'original_total' => $summary['subtotal'],
                    ]);
                }

                if ($summary['voucher'] > 0) {
                    $voucher->markAsUsed($pesanan->id);
                }

                return $pesanan;
            });
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
            return null;
        }

        Pesanan::rememberForVisitor($pesanan);

        $this->cart = [];
        session()->forget('cart');
        $this->dispatch('cartUpdated');
        $this->dispatch('clearLocalCart');
        $this->clearDiscountCode();
        $this->clearVoucher();

        return redirect()->route('pesanan.berhasil', $pesanan->nomor_pesanan);
    }
}
