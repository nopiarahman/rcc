<?php

use App\Livewire\Admin\VoucherPromoRuleManager;
use App\Livewire\DaftarMinuman;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Minuman;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherPromoRule;
use App\Support\HtmlSanitizer;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Livewire\Volt\Volt;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// S2 — deskripsi produk dibersihkan dari script
test('html sanitizer membuang script, atribut event, dan link javascript', function () {
    $clean = HtmlSanitizer::clean('<p onclick="alert(1)">Kopi <strong>enak</strong></p><script>alert(2)</script>'
        . '<img src=x onerror="alert(3)"><a href="javascript:alert(4)">klik</a><a href="https://ok.test">ok</a>');

    expect($clean)
        ->toContain('<p>Kopi <strong>enak</strong></p>')
        ->toContain('<a href="https://ok.test" target="_blank" rel="noopener nofollow">ok</a>')
        ->not->toContain('script')
        ->not->toContain('onclick')
        ->not->toContain('onerror')
        ->not->toContain('javascript:');
});

test('deskripsi teks biasa tetap di-escape dan baris barunya dipertahankan', function () {
    expect(HtmlSanitizer::clean("Kopi & susu\nsegar"))->toBe("Kopi &amp; susu<br />\nsegar");
});

test('halaman detail minuman tidak menjalankan script dari deskripsi', function () {
    $minuman = Minuman::create(['nama' => 'Kopi', 'base_price' => 10000, 'deskripsi' => '<p>Enak</p><script>alert("xss")</script>']);

    $this->get(route('minuman.detail', $minuman->id))
        ->assertOk()
        ->assertSee('<p>Enak</p>', false)
        ->assertDontSee('alert("xss")', false);
});

// S7 — panel detail pesanan admin
test('panel detail pesanan admin menampilkan item botolan dan tetap ada setelah render ulang', function () {
    $this->actingAs(User::factory()->create());
    $pesanan = Pesanan::create(['nama_pemesan' => 'Ani', 'alamat_pengantaran' => 'Takeaway', 'total' => 30000, 'total_harga' => 30000]);
    $pesanan->details()->create(['nama_botolan' => 'Kopi Susu - 1 Liter', 'harga' => 30000, 'qty' => 1, 'subtotal' => 30000]);

    Volt::test('pesanan.index')
        ->call('lihatDetail', $pesanan->id)
        ->assertSee('Kopi Susu - 1 Liter')
        ->call('$refresh') // seperti wire:poll
        ->assertSee('Kopi Susu - 1 Liter');
});

test('status pesanan yang tidak dikenal ditolak', function () {
    $this->actingAs(User::factory()->create());
    $pesanan = Pesanan::create(['nama_pemesan' => 'Ani', 'alamat_pengantaran' => 'Takeaway', 'total' => 5000, 'total_harga' => 5000, 'status' => 'diproses']);

    Volt::test('pesanan.index')->call('updateStatus', $pesanan->id, 'hack');

    expect($pesanan->fresh()->status)->toBe('diproses');
});

// S9 — pengaman promo
test('voucher persen tidak bisa lebih dari 100 persen', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(VoucherPromoRuleManager::class)
        ->call('openCreate')
        ->set('description', 'Salah ketik')
        ->set('min_purchase_amount', '50000')
        ->set('voucher_discount_type', 'percentage')
        ->set('voucher_discount_value', '150')
        ->set('voucher_validity_days', '30')
        ->call('save')
        ->assertHasErrors(['voucher_discount_value' => 'max']);
});

test('potongan voucher persen tidak pernah melebihi total belanja', function () {
    $voucher = new Voucher(['discount_type' => 'percentage', 'discount_value' => 150]);

    expect($voucher->calculateDiscount(20000))->toBe(20000.0)
        ->and($voucher->calculateDiscount(12345.5))->toBeLessThanOrEqual(12345.5);
});

test('total belanja untuk voucher otomatis tidak menghitung ongkir', function () {
    $customer = Customer::create(['nama' => 'Ani', 'no_hp' => '6281', 'password' => bcrypt('x')]);
    VoucherPromoRule::create([
        'description' => 'Tiap 50rb', 'min_purchase_amount' => 50000, 'voucher_discount_type' => 'fixed',
        'voucher_discount_value' => 5000, 'voucher_min_purchase' => 0, 'voucher_validity_days' => 30, 'is_active' => true,
    ]);
    // Belanja 45.000 + ongkir 10.000 = total 55.000, tetapi belanjanya belum 50.000
    $pesanan = Pesanan::create([
        'customer_id' => $customer->id, 'nama_pemesan' => 'Ani', 'alamat_pengantaran' => 'Jl. A',
        'total' => 55000, 'total_harga' => 55000, 'ongkir' => 10000, 'status' => 'selesai',
    ]);

    Voucher::generateForOrder($pesanan);

    expect(Voucher::count())->toBe(0);
});

// S10 — halaman menu tidak N+1
test('jumlah query daftar menu tidak bertambah seiring jumlah produk', function () {
    \App\Models\WebSetting::current(); // pengaturan toko dibuat dulu agar tidak ikut terhitung

    $hitungQuery = function () {
        DB::flushQueryLog();
        DB::enableQueryLog();
        Livewire::test(DaftarMinuman::class, ['kategoris' => []]);
        DB::disableQueryLog();

        return count(DB::getQueryLog());
    };

    foreach (range(1, 2) as $i) {
        $m = Minuman::create(['nama' => "Kopi {$i}", 'base_price' => 10000]);
        Discount::create(['minuman_id' => $m->id, 'name' => 'Promo', 'discount_type' => 'percentage', 'discount_amount' => 10, 'start_date' => now()->subDay(), 'end_date' => now()->addDay(), 'is_active' => true]);
    }
    $hitungQuery(); // pemanasan: view composer menyimpan pengaturan toko di cache statis setelah render pertama
    $sedikit = $hitungQuery();

    foreach (range(3, 12) as $i) {
        $m = Minuman::create(['nama' => "Kopi {$i}", 'base_price' => 10000]);
        Discount::create(['minuman_id' => $m->id, 'name' => 'Promo', 'discount_type' => 'percentage', 'discount_amount' => 10, 'start_date' => now()->subDay(), 'end_date' => now()->addDay(), 'is_active' => true]);
    }

    expect($hitungQuery())->toBe($sedikit);
});
