<?php

use Livewire\Volt\Volt;
use App\Livewire\CartPage;
use App\Livewire\MenuPage;
use App\Livewire\HistoryPage;
use App\Livewire\Minuman\Index;
use App\Livewire\MinumanDetail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontEndController;

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Route untuk sinkronisasi keranjang dari Local Storage ke Session
Route::post('/cart/sync', function (\Illuminate\Http\Request $request) {
    $cart = $request->input('cart', []);
    session(['cart' => $cart]);
    return response()->json(['success' => true, 'cart' => $cart]);
})->name('cart.sync');

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
    Volt::route('minuman/sugar/create', 'sugar.create')->name('minuman.sugar.create');
    Volt::route('minuman/topping/create', 'topping.create')->name('minuman.topping.create');
    
    Volt::route('list-pesanan', 'pesanan.index')->name('pesanan.index');
});


// Front End
Route::get('/', function () {
    return view('welcome-screen');
});
// Route::controller(::class)->group(function () {
//     Route::get('/home','index')->name('home');
// });
Route::get('/home', MenuPage::class)->name('home');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/minuman-detail-{id}',MinumanDetail::class)->name('minuman.detail');
Route::get('/keranjang', CartPage::class)->name('cart');

Route::get('/debug/cart', function () {
    return session('cart');
})->name('debug-cart');
Route::get('/pesanan', HistoryPage::class)->name('pesanan');

// Test route for timezone
Route::get('/test-timezone', function () {
    return [
        'current_time' => now()->format('Y-m-d H:i:s'),
        'timezone' => config('app.timezone'),
        'php_timezone' => date_default_timezone_get(),
    ];
});

// Test route for web settings
Route::get('/test-web-settings', function () {
    $settings = \App\Models\WebSetting::first();
    return [
        'site_name' => $settings->site_name ?? 'Not set',
        'logo_path' => $settings->logo_path ?? 'Not set',
        'favicon_path' => $settings->favicon_path ?? 'Not set',
    ];
});

require __DIR__.'/auth.php';
