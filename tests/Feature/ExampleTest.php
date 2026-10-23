<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('returns a successful response', function () {
    bukaToko24Jam();

    $response = $this->get('/');

    $response->assertStatus(200);
});
