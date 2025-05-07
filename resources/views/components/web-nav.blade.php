<nav class="navbar navbar-expand-md navbar-light bg-white border-bottom shadow-sm d-none d-md-block">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('menu') }}">Raihaan Coffee Corner
        </a>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a wire:navigate class="nav-link {{ request()->routeIs('menu') ? 'active text-primary fw-semibold' : '' }}" href="{{ route('menu') }}">
                        Menu
                    </a>
                </li>
                <li class="nav-item">
                    <a wire:navigate class="nav-link {{ request()->routeIs('cart') ? 'active text-primary fw-semibold' : '' }}" href="{{ route('cart') }}">
                        Keranjang
                    </a>
                </li>
                <li class="nav-item">
                    <a wire:navigate class="nav-link {{ request()->routeIs('history') ? 'active text-primary fw-semibold' : '' }}" href="{{ route('history') }}">
                       Pesanan
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
