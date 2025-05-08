<nav class="navbar navbar-expand-md navbar-light bg-white border-bottom shadow-sm d-none d-md-block">
    <div class="container">
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a wire:navigate class="nav-link {{ request()->routeIs('home') ? 'active text-success fw-semibold' : '' }}" href="{{ route('home') }}">
                        Menu
                    </a>
                </li>
                <li class="nav-item">
                    <a wire:navigate class="nav-link {{ request()->routeIs('cart') ? 'active text-success fw-semibold' : '' }}" href="{{ route('cart') }}">
                        Keranjang
                    </a>
                </li>
                <li class="nav-item">
                    <a wire:navigate class="nav-link {{ request()->routeIs('history') ? 'active text-success fw-semibold' : '' }}" href="{{ route('history') }}">
                       Pesanan
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
