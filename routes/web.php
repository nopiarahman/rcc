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
use App\Http\Middleware\CheckStoreOpen;

// Maintenance route (must be outside any middleware group)
Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');

// Auth routes (must be accessible when store is closed)
Route::middleware(['web'])->group(function () {
    require __DIR__.'/auth.php';
});

// Dashboard route (only for authenticated users)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

// Apply store open check to all routes except auth and maintenance
Route::middleware(['store.open'])->group(function () {
    // Route untuk sinkronisasi keranjang dari Local Storage ke Session
    Route::post('/cart/sync', function (\Illuminate\Http\Request $request) {
        $cart = $request->input('cart', []);
        session(['cart' => $cart]);
        return response()->json(['success' => true, 'cart' => $cart]);
    })->name('cart.sync');

    // Front End Routes
    Route::get('/', function () {
        return view('welcome-screen');
    });

    Route::get('/home', MenuPage::class)->name('home');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/minuman-detail-{id}', MinumanDetail::class)->name('minuman.detail');
    Route::get('/keranjang', CartPage::class)->name('cart');
    Route::get('/pesanan', HistoryPage::class)->name('pesanan');

    Route::get('/debug/cart', function () {
        return session('cart');
    })->name('debug-cart');

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


// Auth routes are loaded above and are accessible even when store is closed
// No additional routes should be defined outside the middleware groups to ensure proper store closure handling
