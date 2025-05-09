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

Route::middleware(['auth'])->group(function () {
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

// Route::get('/debug/cart', function () {
//     return session('cart');
// })->name('debug-cart');
Route::get('/pesanan', HistoryPage::class)->name('history');

require __DIR__.'/auth.php';
