<?php

use App\Livewire\CartPage;
use App\Livewire\HistoryPage;
use App\Livewire\MinumanDetail;
use App\Models\Customer;
use App\Models\DiscountCode;
use App\Models\Minuman;
use App\Models\Pesanan;
use App\Models\Size;
use App\Models\Sugar;
use App\Models\Voucher;
use App\Models\WebSetting;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    WebSetting::current()->update([
        'order_mode' => 'both',
        'whatsapp_number' => '6281234567890',
        'latitude' => -1.66651,
        'longitude' => 103.65238,
        'delivery_radius' => 5000,
        'ongkir_enabled' => true,
        'ongkir_per_km' => 2000,
        'ongkir_free_km' => 1,
        'opening_time' => '00:00:00', // jam buka = jam tutup → buka 24 jam
        'closing_time' => '00:00:00',
        'is_temporarily_closed' => false,
    ]);

    Http::fake(['*' => Http::response(['routes' => [['distance' => 3000]]])]);

    $this->kopi = Minuman::create(['nama' => 'Es Kopi Susu', 'base_price' => 15000, 'is_habis' => false]);
});

function cartWith(Minuman $minuman, int $qty = 1, array $extra = []): void
{
    session(['cart' => [array_merge(['type' => 'minuman', 'id' => $minuman->id, 'qty' => $qty], $extra)]]);
}

function takeawayCheckout()
{
    return Livewire::test(CartPage::class)
        ->call('selectOrderType', 'takeaway')
        ->set('nama_pemesan', 'Budi')
        ->set('waktu_pengantaran', '10.00 WIB');
}

// K1 — harga dari browser diabaikan
test('checkout memakai harga dari database, bukan harga dari keranjang', function () {
    cartWith($this->kopi, 3, ['price' => 1]);

    takeawayCheckout()->call('konfirmasiCheckout')->assertHasNoErrors();

    $pesanan = Pesanan::with('details')->sole();
    expect((int) $pesanan->total)->toBe(45000)
        ->and((int) $pesanan->details->first()->harga)->toBe(15000);
});

test('cart sync membuang harga dan item yang tidak valid', function () {
    $this->postJson('/cart/sync', ['cart' => [
        ['type' => 'minuman', 'id' => $this->kopi->id, 'qty' => 2, 'price' => 1, 'model' => 'x'],
        ['type' => 'minuman', 'id' => $this->kopi->id, 'qty' => -5],
        ['type' => 'hacker', 'id' => 1, 'qty' => 1],
    ]])->assertOk();

    $cart = array_values(session('cart'));
    expect($cart)->toHaveCount(1)
        ->and($cart[0])->not->toHaveKey('price')
        ->and($cart[0]['qty'])->toBe(2);
});

test('qty dibatasi maksimal 50', function () {
    cartWith($this->kopi, 999);

    takeawayCheckout()->call('konfirmasiCheckout');

    expect(Pesanan::sole()->details->first()->qty)->toBe(50);
});

test('item yang habis dikeluarkan dari keranjang dan tidak bisa dipesan', function () {
    cartWith($this->kopi);
    $this->kopi->update(['is_habis' => true]);

    takeawayCheckout()->call('konfirmasiCheckout');

    expect(Pesanan::count())->toBe(0);
});

// UI1 — harga tampilan sama dengan harga keranjang
test('ukuran default yang tidak terpasang tidak ikut dihitung di menu maupun keranjang', function () {
    $medium = Size::create(['name' => 'Medium', 'price' => 3000]);
    $gula = Sugar::create(['level' => 'Normal', 'price' => 1000]);
    $this->kopi->update(['default_size_id' => $medium->id, 'default_sugar_id' => $gula->id]);

    expect((int) $this->kopi->fresh()->default_price)->toBe(15000);

    Livewire::test(MinumanDetail::class, ['id' => $this->kopi->id])->call('addToCart');
    takeawayCheckout()->call('konfirmasiCheckout');

    $detail = Pesanan::sole()->details->first();
    expect((int) $detail->harga)->toBe(15000)->and($detail->size)->toBeNull()->and($detail->gula)->toBeNull();
});

test('pilihan ukuran yang tidak terpasang di minuman ditolak', function () {
    $large = Size::create(['name' => 'Large', 'price' => 5000]);

    Livewire::test(MinumanDetail::class, ['id' => $this->kopi->id])
        ->set('selectedSizeId', $large->id)
        ->call('addToCart');

    expect(session('cart', []))->toBeEmpty();
});

// K2 — ongkir & voucher tidak bisa dimanipulasi
test('ongkir dan voucher terkunci dari browser', function (string $property, mixed $value) {
    cartWith($this->kopi);

    expect(fn () => Livewire::test(CartPage::class)->set($property, $value))
        ->toThrow(CannotUpdateLockedPropertyException::class);
})->with([
    'ongkir' => ['ongkir', -19000],
    'voucher' => ['applied_voucher_id', 1],
    'keranjang' => ['cart', []],
]);

test('voucher milik pelanggan lain tidak bisa dipakai', function () {
    $pemilik = Customer::create(['nama' => 'Pemilik', 'no_hp' => '6281111', 'password' => bcrypt('x')]);
    $penyerang = Customer::create(['nama' => 'Penyerang', 'no_hp' => '6282222', 'password' => bcrypt('x')]);
    $voucher = Voucher::create(['customer_id' => $pemilik->id, 'code' => 'VCH1', 'discount_type' => 'fixed', 'discount_value' => 10000]);

    Auth::guard('customer')->login($penyerang);
    cartWith($this->kopi, 2);

    takeawayCheckout()->call('applyVoucher', $voucher->id)->call('konfirmasiCheckout');

    expect((int) Pesanan::sole()->voucher_amount)->toBe(0)
        ->and($voucher->fresh()->is_used)->toBeFalse();
});

// T1 — transaksi & batas pemakaian
test('kode diskon yang kuotanya habis saat checkout ditolak dan pesanan tidak dibuat', function () {
    $kode = DiscountCode::create([
        'code' => 'HEMAT', 'name' => 'Hemat', 'discount_type' => 'fixed', 'discount_amount' => 5000,
        'minimum_purchase' => 0, 'max_redeem' => 1, 'used_count' => 0,
        'start_date' => now()->subDay(), 'end_date' => now()->addDay(), 'is_active' => true,
    ]);
    cartWith($this->kopi, 2);

    $page = takeawayCheckout()->set('discount_code', 'HEMAT')->call('applyDiscountCode')->assertSet('discount_error', '');
    $kode->update(['used_count' => 1]); // dipakai orang lain sebelum checkout

    $page->call('konfirmasiCheckout')->assertHasErrors('checkout');

    expect(Pesanan::count())->toBe(0)->and($kode->fresh()->used_count)->toBe(1);
});

test('voucher yang sudah terpakai di tab lain ditolak', function () {
    $customer = Customer::create(['nama' => 'Ani', 'no_hp' => '6283333', 'password' => bcrypt('x')]);
    $voucher = Voucher::create(['customer_id' => $customer->id, 'code' => 'VCH2', 'discount_type' => 'fixed', 'discount_value' => 5000]);
    Auth::guard('customer')->login($customer);
    cartWith($this->kopi, 2);

    $page = takeawayCheckout()->call('applyVoucher', $voucher->id);
    $voucher->update(['is_used' => true]);

    $page->call('konfirmasiCheckout')->assertHasErrors('checkout');
    expect(Pesanan::count())->toBe(0);
});

test('kode diskon dengan batas per pelanggan tidak bisa dipakai tamu', function () {
    DiscountCode::create([
        'code' => 'MEMBER', 'name' => 'Member', 'discount_type' => 'fixed', 'discount_amount' => 5000,
        'minimum_purchase' => 0, 'max_per_customer' => 1, 'used_count' => 0,
        'start_date' => now()->subDay(), 'end_date' => now()->addDay(), 'is_active' => true,
    ]);
    cartWith($this->kopi);

    takeawayCheckout()->set('discount_code', 'MEMBER')->call('applyDiscountCode')
        ->assertSet('applied_discount_id', null)
        ->assertSee('khusus pelanggan terdaftar');
});

// T2 — total yang ditampilkan sama dengan yang dicatat
test('total di layar sama dengan total yang tersimpan saat diskon dan voucher dipakai bersamaan', function () {
    $customer = Customer::create(['nama' => 'Ani', 'no_hp' => '6284444', 'password' => bcrypt('x')]);
    $voucher = Voucher::create(['customer_id' => $customer->id, 'code' => 'PERSEN', 'discount_type' => 'percentage', 'discount_value' => 10]);
    DiscountCode::create([
        'code' => 'POTONG', 'name' => 'Potong', 'discount_type' => 'fixed', 'discount_amount' => 7000,
        'minimum_purchase' => 0, 'used_count' => 0,
        'start_date' => now()->subDay(), 'end_date' => now()->addDay(), 'is_active' => true,
    ]);
    Auth::guard('customer')->login($customer);
    cartWith($this->kopi, 3); // 45.000

    $page = takeawayCheckout()->call('applyVoucher', $voucher->id)
        ->set('discount_code', 'POTONG')->call('applyDiscountCode');

    // 45.000 - 7.000 = 38.000; voucher 10% dari 38.000 = 3.800; 34.200 dibulatkan ke bawah = 34.000
    $page->assertSee('Rp34.000');
    $page->call('konfirmasiCheckout');

    expect((int) Pesanan::sole()->total)->toBe(34000)
        ->and((int) Pesanan::sole()->voucher_amount)->toBe(3800);
});

// S4 — radius & lokasi dicek di server
test('delivery tanpa lokasi tidak bisa checkout', function () {
    cartWith($this->kopi);

    Livewire::test(CartPage::class)
        ->call('selectOrderType', 'delivery')
        ->set('alamat_pengantaran', 'Jl. Mawar 1')
        ->call('checkout')
        ->assertHasErrors('lokasi');
});

test('lokasi di luar radius ditolak dan ongkir dihitung untuk lokasi dalam radius', function () {
    cartWith($this->kopi);

    Livewire::test(CartPage::class)
        ->call('selectOrderType', 'delivery')
        ->call('setCustomerLocation', -6.2, 106.8) // Jakarta, jauh di luar radius
        ->assertSet('customer_lat', null)
        ->assertSee('di luar jangkauan')
        ->call('setCustomerLocation', -1.67, 103.66)
        ->assertSet('ongkir', 4000); // 3 km jalan raya - 1 km gratis = 2 km x 2.000
});

// S3 — toko tutup
test('checkout ditolak saat toko tutup sementara', function () {
    cartWith($this->kopi);
    WebSetting::current()->update(['is_temporarily_closed' => true]);

    takeawayCheckout()->call('konfirmasiCheckout')->assertHasErrors('checkout');
    expect(Pesanan::count())->toBe(0);
});

// UI5 & K4 — halaman konfirmasi dan riwayat hanya untuk pemilik pesanan
test('setelah checkout pembeli diarahkan ke halaman konfirmasi miliknya', function () {
    cartWith($this->kopi);

    takeawayCheckout()->call('konfirmasiCheckout')
        ->assertRedirect(route('pesanan.berhasil', Pesanan::sole()->nomor_pesanan));
});

test('halaman konfirmasi menampilkan nomor pesanan dan tombol WhatsApp untuk pemiliknya', function () {
    $pesanan = Pesanan::create([
        'nama_pemesan' => 'Budi', 'alamat_pengantaran' => 'Takeaway', 'total' => 15000,
        'total_harga' => 15000, 'session_id' => 'sesi-checkout', 'order_type' => 'takeaway',
    ]);
    $pesanan->details()->create(['minuman_id' => $this->kopi->id, 'nama_minuman' => 'Es Kopi Susu', 'harga' => 15000, 'qty' => 1, 'subtotal' => 15000]);

    // Browser pembeli membawa cookie riwayat yang diset saat checkout
    $this->withCookie(Pesanan::VISITOR_COOKIE, json_encode([$pesanan->id]))
        ->get(route('pesanan.berhasil', $pesanan->nomor_pesanan))
        ->assertOk()
        ->assertSee($pesanan->nomor_pesanan)
        ->assertSee('Kirim Pesanan via WhatsApp')
        ->assertSee('wa.me/6281234567890', false);
});

test('halaman konfirmasi pesanan orang lain tidak bisa dibuka', function () {
    $milikOrangLain = Pesanan::create([
        'nama_pemesan' => 'Orang Lain', 'alamat_pengantaran' => 'Rumah Rahasia', 'total' => 5000,
        'total_harga' => 5000, 'session_id' => 'sesi-lain',
    ]);

    $this->get(route('pesanan.berhasil', $milikOrangLain->nomor_pesanan))->assertNotFound();
});

test('riwayat pesanan tidak bisa membuka pesanan milik orang lain', function () {
    $milikOrangLain = Pesanan::create([
        'nama_pemesan' => 'Orang Lain', 'alamat_pengantaran' => 'Rumah Rahasia', 'total' => 5000,
        'total_harga' => 5000, 'session_id' => 'sesi-lain',
    ]);

    expect(fn () => Livewire::test(HistoryPage::class)->call('viewOrder', $milikOrangLain->id))
        ->toThrow(ModelNotFoundException::class);
});

// S6 — riwayat tamu tetap ada walau sesi berganti
test('riwayat tamu tetap terlihat lewat cookie walau sesi sudah berganti', function () {
    $pesanan = Pesanan::create([
        'nama_pemesan' => 'Tamu', 'alamat_pengantaran' => 'Takeaway', 'total' => 5000,
        'total_harga' => 5000, 'session_id' => 'sesi-lama-yang-sudah-habis',
    ]);

    Livewire::withCookie(Pesanan::VISITOR_COOKIE, json_encode([$pesanan->id]))
        ->test(HistoryPage::class)
        ->call('viewOrder', $pesanan->id)
        ->assertSet('selectedOrder.id', $pesanan->id);
});
