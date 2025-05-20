<nav class="navbar navbar-expand-md navbar-light bg-white border-bottom shadow-sm d-none d-md-block">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            @if($webSettings->logo_path ?? false)
                <img src="{{ asset('storage/' . $webSettings->logo_path) }}" alt="{{ $webSettings->site_name ?? 'Logo' }}" height="40" class="d-inline-block align-text-top me-2">
            @endif
            <span class="fw-bold">{{ $webSettings->site_name ?? 'Raihaan Coffee Corner' }}</span>
        </a>
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
