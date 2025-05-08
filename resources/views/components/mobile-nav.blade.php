<div>
    <style>
        .mobile-nav {
            padding: 4px 0;
        }
        .mobile-nav .nav-item {
            padding: 6px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }
        .mobile-nav .material-symbols-outlined {
            font-size: 20px;
            line-height: 1;
            margin-bottom: 2px;
        }
        .mobile-nav .small {
            font-size: 0.7rem;
            line-height: 1;
            margin: 0;
        }
    </style>
    @php
        $cart = session('cart', []);
        $totalItems = collect($cart)->sum('qty'); // pastikan setiap item punya key 'qty'
    @endphp
    <nav class="mobile-nav d-flex d-md-none shadow-sm bg-white border-top position-fixed bottom-0 start-0 end-0">
        <a href="{{ route('home') }}" wire:navigate class="nav-link nav-item flex-fill text-center {{ request()->routeIs('home') ? 'active text-success' : '' }} ">
            <i style="font-variation-settings: 'FILL' 1, 'wght' 700, 'GRAD' 0, 'opsz' 48;" class="material-symbols-outlined ">home</i>
            <span class="small mt-n1">Home</span>
        </a>
        <a href="{{ route('cart') }}" wire:navigate class="nav-link nav-item flex-fill text-center position-relative {{ request()->routeIs('cart') ? 'active text-success' : '' }}">
            <i class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1, 'wght' 700, 'GRAD' 0, 'opsz' 48;">
                shopping_bag
            </i> 
            @if ($totalItems > 0)
                <span class="position-absolute top-0 badge rounded-pill bg-danger" style="font-size: 0.6rem;right:35%">
                    {{ $totalItems }}
                </span>
            @endif
            <span class="small">Keranjang</span>
        </a>
        <a href="{{ route('history') }}" wire:navigate class="nav-link nav-item flex-fill text-center {{ request()->routeIs('history') ? 'active text-success' : '' }}">
            <span class="material-symbols-outlined">
                receipt_long
                </span>
            <span class="small">Pesanan</span>
        </a>
    </nav>
</div>