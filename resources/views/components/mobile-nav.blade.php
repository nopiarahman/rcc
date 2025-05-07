<div>
    <nav class="mobile-nav d-flex d-md-none shadow-sm bg-white border-top position-fixed bottom-0 start-0 end-0">
        <a href="{{ route('menu') }}" wire:navigate class="nav-link nav-item flex-fill text-center {{ request()->routeIs('menu') ? 'active text-success' : '' }} ">
            <i style="font-variation-settings: 'FILL' 1, 'wght' 700, 'GRAD' 0, 'opsz' 48;" class="material-symbols-outlined ">home</i><br>
            <span class="small mt-n1">Home</span>
        </a>
        <a href="{{ route('cart') }}" wire:navigate class="nav-link nav-item flex-fill text-center {{ request()->routeIs('cart') ? 'active text-success' : '' }}">
            <i class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1, 'wght' 700, 'GRAD' 0, 'opsz' 48;">
                shopping_bag</i> <br>
            <span class="small">Keranjang</span>
        </a>
        <a href="{{ route('history') }}" wire:navigate class="nav-link nav-item flex-fill text-center {{ request()->routeIs('history') ? 'active text-success' : '' }}">
            <span class="material-symbols-outlined">
                receipt_long
                </span> <br>
            <span class="small">Pesanan</span>
        </a>
    </nav>
</div>