<?php

use App\Livewire\CustomerAuth\Login;
use App\Livewire\CustomerAuth\Register;
use App\Models\Customer;
use App\Models\Pesanan;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// T4 — nomor yang sudah terdaftar dalam format lain tidak menyebabkan error 500
test('registrasi dengan nomor 08xx yang sudah terdaftar sebagai 628xx ditolak dengan pesan', function () {
    Customer::create(['nama' => 'Lama', 'no_hp' => '6281234567890', 'password' => Hash::make('rahasia')]);

    Livewire::test(Register::class)
        ->set('nama', 'Baru')
        ->set('no_hp', '0812-3456-7890')
        ->set('password', 'rahasia1')
        ->set('password_confirmation', 'rahasia1')
        ->call('register')
        ->assertHasErrors(['no_hp' => 'unique']);

    expect(Customer::count())->toBe(1);
});

test('registrasi baru menyimpan nomor dalam format 62 dan bisa langsung login', function () {
    Livewire::test(Register::class)
        ->set('nama', 'Siti')
        ->set('no_hp', '0813 1111 2222')
        ->set('password', 'rahasia1')
        ->set('password_confirmation', 'rahasia1')
        ->call('register')
        ->assertRedirect(route('customer.account'));

    expect(Customer::sole()->no_hp)->toBe('6281311112222')
        ->and(Auth::guard('customer')->check())->toBeTrue();
});

// T3 — batas percobaan
test('login pelanggan dibatasi 5 percobaan per menit', function () {
    Customer::create(['nama' => 'Ani', 'no_hp' => '6281200000000', 'password' => Hash::make('benar123')]);

    $page = Livewire::test(Login::class)->set('no_hp', '081200000000')->set('password', 'salah');
    foreach (range(1, 5) as $_) {
        $page->call('login');
    }

    // Percobaan ke-6 dengan password benar tetap ditolak karena sedang dibatasi
    $page->set('password', 'benar123')->call('login')->assertHasErrors('no_hp')->assertSee('Terlalu banyak percobaan');
    expect(Auth::guard('customer')->check())->toBeFalse();
});

test('registrasi dibatasi per perangkat', function () {
    foreach (range(1, 6) as $i) {
        Livewire::test(Register::class)
            ->set('nama', "Akun {$i}")
            ->set('no_hp', '08130000000' . $i)
            ->set('password', 'rahasia1')
            ->set('password_confirmation', 'rahasia1')
            ->call('register');
        Auth::guard('customer')->logout();
    }

    expect(Customer::count())->toBe(5);
});

test('login menghubungkan pesanan tamu dari cookie riwayat ke akun', function () {
    $customer = Customer::create(['nama' => 'Ani', 'no_hp' => '6281200000001', 'password' => Hash::make('benar123')]);
    $pesanan = Pesanan::create(['nama_pemesan' => 'Ani', 'alamat_pengantaran' => 'Takeaway', 'total' => 5000, 'total_harga' => 5000, 'session_id' => 'sesi-lama']);

    Livewire::withCookie(Pesanan::VISITOR_COOKIE, json_encode([$pesanan->id]))
        ->test(Login::class)
        ->set('no_hp', '081200000001')->set('password', 'benar123')
        ->call('login');

    expect($pesanan->fresh()->customer_id)->toBe($customer->id);
});

// K3 — seeder tidak lagi berisi password, dan tidak menimpa akun yang sudah ada
test('seeder admin memakai env dan tidak mengubah password akun yang sudah ada', function () {
    $lama = User::factory()->create(['email' => 'admin@example.com', 'password' => 'password-lama']);

    $this->seed(UserSeeder::class);

    expect(User::count())->toBe(1)
        ->and(Hash::check('password-lama', $lama->fresh()->password))->toBeTrue();
});

test('kode tidak lagi berisi password admin', function () {
    expect(file_get_contents(database_path('seeders/UserSeeder.php')))
        ->not->toContain('hanumcoffee')
        ->not->toContain('123Jajan!')
        ->and(file_exists(base_path('create-user.php')))->toBeFalse();
});
