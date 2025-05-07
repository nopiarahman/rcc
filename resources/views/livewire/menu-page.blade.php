<!-- resources/views/livewire/menu-page.blade.php -->
@extends('layouts.public')

@section('content')
<div class="mt-3">
    <div class="d-none d-md-block mb-4">
        <x-web-nav />
    </div>
    <h5 class="mb-3">Menu Minuman</h5>
    <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center">
            Es Kopi Susu
            <button class="btn btn-sm btn-outline-primary">+ Tambah</button>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            Matcha Latte asd
            <button class="btn btn-sm btn-outline-primary">+ Tambah</button>
        </li>
    </ul>
    <x-mobile-nav/>
</div>
@endsection