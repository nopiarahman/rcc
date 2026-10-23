<?php

use Livewire\Volt\Volt;
use App\Livewire\CartPage;
use App\Livewire\MenuPage;
use App\Livewire\HistoryPage;
use App\Livewire\Minuman\Index;
use App\Livewire\MinumanDetail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontEndController;
use App\Http\Middleware\CheckStoreOpen;
use App\Services\CartService;

// Maintenance route (must be outside any middleware group)
Route::get('/maintenance', function (\Illuminate\Http\Request $request) {
    $settings = \App\Models\WebSetting::current();

    if ($settings->isOpenNow()) {
        return redirect()->route('home');
    }

    return view('maintenance', ['reason' => $settings->closedReason()]);
})->name('maintenance');

// Auth routes (must be accessible when store is closed)
Route::middleware(['web'])->group(function () {
    require __DIR__.'/auth.php';
});

// Dashboard route (only for authenticated users)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

// Customer-facing routes (always accessible regardless of store hours)
Route::middleware(['web'])->group(function () {
    Route::get('/akun/masuk', \App\Livewire\CustomerAuth\Login::class)->name('customer.login');
    Route::get('/akun/daftar', \App\Livewire\CustomerAuth\Register::class)->name('customer.register');
    Route::get('/akun', \App\Livewire\CustomerAccount::class)->name('customer.account');
    Route::post('/akun/keluar', function () {
        \Illuminate\Support\Facades\Auth::guard('customer')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('home');
    })->name('customer.logout');

    // Halaman setelah checkout: nomor pesanan + tombol kirim ke WhatsApp (tetap bisa dibuka walau toko sudah tutup)
    Route::get('/pesanan/{nomor}/berhasil', \App\Livewire\PesananBerhasil::class)->name('pesanan.berhasil');
});

// Apply store open check to all routes except auth and maintenance
Route::middleware(['store.open'])->group(function () {
    // Route untuk sinkronisasi keranjang dari Local Storage ke Session.
    // Hanya ID, pilihan, dan jumlah yang disimpan; harga dari browser dibuang.
    Route::post('/cart/sync', function (\Illuminate\Http\Request $request) {
        $cart = CartService::sanitize((array) $request->input('cart', []));
        session(['cart' => $cart]);
        return response()->json(['success' => true, 'cart' => array_values($cart)]);
    })->name('cart.sync');

    // Front End Routes
    Route::get('/', function () {
        return view('welcome-screen');
    });

    Route::get('/home', MenuPage::class)->name('home');
    Route::get('/minuman-detail-{id}', MinumanDetail::class)->name('minuman.detail');
    Route::get('/makanan-detail-{makanan}', \App\Livewire\MakananDetail::class)->name('makanan.detail');
    Route::get('/pesan-botolan', \App\Livewire\BotolonPage::class)->name('botolan.page');
    Route::get('/keranjang', CartPage::class)->name('cart');
    Route::get('/pesanan', HistoryPage::class)->name('pesanan');
});

// Admin routes (protected by auth middleware)
Route::middleware(['auth'])->group(function () {
    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Welcome Images Management using Livewire
        Route::get('/welcome-images', \App\Livewire\Admin\WelcomeImageManagement::class)
            ->name('welcome-images');
    });

    Route::get('/dashboard/banners', \App\Livewire\Admin\BannerManagement::class)
        ->name('dashboard.banners');
    Route::get('/dashboard/web-settings', \App\Livewire\Admin\WebSettings::class)
        ->name('dashboard.web-settings');
    Route::get('/dashboard/discounts', \App\Livewire\Admin\DiscountManager::class)
        ->name('dashboard.discounts');
    Route::get('/dashboard/discount-codes', \App\Livewire\Admin\DiscountCodeManager::class)
        ->name('dashboard.discount-codes');
    Route::get('/dashboard/popups', \App\Livewire\Admin\PopupManagement::class)
        ->name('dashboard.popups');
    Route::get('/dashboard/voucher-promo-rules', \App\Livewire\Admin\VoucherPromoRuleManager::class)
        ->name('dashboard.voucher-promo-rules');
    Route::get('/dashboard/customers', \App\Livewire\Admin\CustomerManager::class)
        ->name('dashboard.customers');
    Route::get('/dashboard/users', \App\Livewire\Admin\UserManagement::class)
        ->name('dashboard.users');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::redirect('settings', 'settings/profile');
    Route::get('minuman', Index::class)->name('minuman.index');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Volt::route('minuman/create', 'minuman.create')->name('minuman.create');
    Volt::route('minuman/{minuman}/edit', 'minuman.edit')->name('minuman.edit');
    Volt::route('minuman/bahan/create', 'bahan.create')->name('minuman.bahan.create');
    Volt::route('minuman/sizes/create', 'sizes.create')->name('minuman.sizes.create');

    // Makanan routes
    Volt::route('makanan', 'makanan.index')->name('makanan.index');
    Volt::route('makanan/create', 'makanan.create')->name('makanan.create');
    Volt::route('makanan/{makanan}/edit', 'makanan.edit')->name('makanan.edit');
    Route::get('makanan/bahan', \App\Livewire\Makanan\BahanCrud::class)->name('makanan.bahan-crud');
    Route::get('makanan/topping', \App\Livewire\Makanan\ToppingCrud::class)->name('makanan.topping-crud');

    // Botolan routes
    Route::get('botolan', \App\Livewire\Botolan\Index::class)->name('botolan.index');
    Route::get('botolan/create', \App\Livewire\Botolan\Create::class)->name('botolan.create');
    Route::get('botolan/{botolan}/edit', \App\Livewire\Botolan\Edit::class)->name('botolan.edit');

    Volt::route('minuman/sugar/create', 'sugar.create')->name('minuman.sugar.create');
    Volt::route('minuman/topping/create', 'topping.create')->name('minuman.topping.create');
    
    Volt::route('list-pesanan', 'pesanan.index')->name('pesanan.index');
});


// Order completion magic link (signed, no auth required — owner taps from WhatsApp).
// GET hanya menampilkan konfirmasi; pesanan baru diselesaikan lewat tombol (POST),
// supaya preview link WhatsApp tidak otomatis mengubah status.
Route::get('/order/{pesanan}/selesai', function (\App\Models\Pesanan $pesanan) {
    return view('order-selesai', ['pesanan' => $pesanan]);
})->name('order.selesai')->middleware('signed');

Route::post('/order/{pesanan}/selesai', function (\App\Models\Pesanan $pesanan) {
    if (!in_array($pesanan->status, ['selesai', 'dibatalkan'])) {
        $pesanan->update([
            'status'        => 'selesai',
            'waktu_selesai' => now(),
        ]);
        \App\Models\Voucher::generateForOrder($pesanan);
    }
    return view('order-selesai', ['pesanan' => $pesanan->fresh()]);
})->name('order.selesai.store')->middleware('signed');

// Auth routes are loaded above and are accessible even when store is closed
// No additional routes should be defined outside the middleware groups to ensure proper store closure handling
