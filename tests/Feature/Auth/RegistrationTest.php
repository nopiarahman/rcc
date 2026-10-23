<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// Registrasi admin sengaja dimatikan; akun admin dibuat lewat halaman Data Admin.
test('registration screen is not available', function () {
    $this->get('/register')->assertNotFound();
});

test('fortify registration endpoint is not available', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    $this->assertGuest();
});
