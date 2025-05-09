<div class="container p-4 bg-white mb-5" style="height: 100vh; display: flex; flex-direction: column;">
    <style>
            @keyframes fadeSlideUp {
        0% {
            opacity: 0;
            transform: translateY(60px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .footer {
                animation: fadeSlideUp 0.6s ease-out both;
            }
    </style>
    {{-- Header (Tetap di Atas) --}}
    <div class="header-button d-flex justify-content-between align-items-center pb-3">
        <a wire:navigate href="{{ route('home') }}" class="btn btn-light rounded-circle shadow-sm">
            <i class="fas fa-chevron-left"></i>
        </a>
        <h5 class="fw-bold pt-3 pe-2">Keranjang</h5>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 small">Jumlah Keranjang: {{ count($cart) }}</h5>
        <button wire:click="clearCart" class="btn btn-link text-danger text-decoration-none btn-sm">Kosongkan Keranjang</button>
    </div>

    {{-- Scrollable Cart --}}
    <div class="flex-grow-1 overflow-auto pe-1">
        @forelse($cartItems as $item)
            <div class="card mb-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ $item['minuman_model']?->getFirstMediaUrl('foto') ?? asset('default-drink.jpg') }}"
                             class="rounded me-3"
                             alt="{{ $item['minuman'] }}"
                             width="60" height="60"
                             style="object-fit: cover; object-position: center;">
                        <div class="text-muted" style="font-size: 10pt">
                            <h6 class="mb-1 fw-bold text-success">{{ $item['minuman'] }}</h6>
                            <small>Size: {{ $item['size'] }} | Gula: {{ $item['sugar'] }} | Topping: {{ $item['topping'] }}</small><br>
                            <small class="text-muted">{{ number_format($item['price'], 0, ',', '.') }} x {{ $item['qty'] }}</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="btn-group btn-group-sm mb-2">
                            <button wire:click="decreaseQty('{{ $item['key'] }}')" class="btn btn-link text-decoration-none text-success">−</button>
                            <button class="btn btn-light" disabled>{{ $item['qty'] }}</button>
                            <button wire:click="increaseQty('{{ $item['key'] }}')" class="btn btn-link text-decoration-none text-success">+</button>
                        </div>
                        <br>
                        <button wire:click="removeItem('{{ $item['key'] }}')" class="btn btn-sm btn-outline-danger">Hapus</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">Keranjang kosong.</div>
        @endforelse
    </div>
{{-- Footer (tetap di bawah) --}}
<div class="footer fixed-bottom bg-white p-3 shadow-lg" style="margin-bottom: 2.5rem">
    <div class="text-center mb-2">
        <h5 class="fw-bold mb-1">Total: {{ number_format($total, 0, ',', '.') }} IDR</h5>
        <p class="text-muted small mb-2">Pastikan pesanan anda benar, siapkan uang pas jika memungkinkan, Jazakallahu khairan</p>
    </div>
    <button class="btn btn-success w-100 mb-2">Checkout</button>
</div>
<x-mobile-nav/>

</div>
