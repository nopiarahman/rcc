<?php

use App\Models\User;
use Livewire\Volt\Volt;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/settings/profile')->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Volt::test('settings.profile')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Volt::test('settings.profile')
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

// S8 — admin tidak lagi bisa menghapus akunnya sendiri dari halaman profil
test('halaman profil tidak menampilkan tombol hapus akun', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/settings/profile')->assertOk()->assertDontSee('Delete account');
});

test('menghapus akun admin tidak ikut menghapus pesanan yang pernah dibuatnya', function () {
    $admin = User::factory()->create();
    $pesanan = \App\Models\Pesanan::create([
        'user_id' => $admin->id, 'nama_pemesan' => 'Walk-in', 'alamat_pengantaran' => 'Takeaway',
        'total' => 10000, 'total_harga' => 10000,
    ]);

    $admin->delete();

    expect($pesanan->fresh())->not->toBeNull()->and($pesanan->fresh()->user_id)->toBeNull();
});