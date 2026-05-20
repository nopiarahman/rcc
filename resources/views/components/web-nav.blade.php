<style>
    .web-nav .nav-link {
        color: var(--text-color, #1a1a1a);
        font-weight: 500;
        font-size: 0.95rem;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        position: relative;
        transition: color 0.15s, background-color 0.15s;
    }
    .web-nav .nav-link:hover {
        color: var(--button-bg-color, #006a3e);
        background-color: rgba(0,0,0,0.04);
    }
    .web-nav .nav-link.active {
        color: var(--button-bg-color, #006a3e) !important;
        font-weight: 600;
    }
    .web-nav .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 1rem;
        right: 1rem;
        height: 2px;
        background-color: var(--button-bg-color, #006a3e);
        border-radius: 2px;
    }
    .web-nav .navbar-brand span {
        color: var(--text-color, #1a1a1a);
        font-size: 1.1rem;
    }
    .web-nav .nav-link-account {
        border-radius: 8px;
        padding: 0.4rem 0.9rem;
    }
    .web-nav .nav-link-account.active::after {
        display: none;
    }
    .web-nav .nav-link-account.active {
        background-color: rgba(0,0,0,0.05);
    }
</style>

<nav class="web-nav navbar navbar-expand-md navbar-light bg-white border-bottom shadow-sm d-none d-md-flex">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}" wire:navigate>
            @if($webSettings->logo_path ?? false)
                <img src="{{ asset('storage/' . $webSettings->logo_path) }}"
                     alt="{{ $webSettings->site_name ?? 'Logo' }}"
                     height="38"
                     class="d-inline-block">
            @endif
            <span class="fw-bold">{{ $webSettings->site_name ?? 'Raihaan Coffee Corner' }}</span>
        </a>

        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav align-items-center gap-1">
                <li class="nav-item">
                    <a wire:navigate href="{{ route('home') }}"
                       class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        Menu
                    </a>
                </li>
                <li class="nav-item">
                    <a wire:navigate href="{{ route('cart') }}"
                       class="nav-link {{ request()->routeIs('cart') ? 'active' : '' }}">
                        Keranjang
                    </a>
                </li>
                <li class="nav-item">
                    @if(auth('customer')->check())
                        <a wire:navigate href="{{ route('customer.account') }}"
                           class="nav-link nav-link-account d-flex align-items-center gap-2 {{ request()->routeIs('customer.account') ? 'active' : '' }}">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle fw-bold text-white flex-shrink-0"
                                  style="width:30px;height:30px;font-size:0.7rem;background:var(--button-bg-color,#006a3e);">
                                {{ auth('customer')->user()->initials() }}
                            </span>
                            <span class="text-truncate" style="max-width:120px;">
                                {{ auth('customer')->user()->nama }}
                            </span>
                        </a>
                    @else
                        <a wire:navigate href="{{ route('customer.login') }}"
                           class="nav-link nav-link-account {{ request()->routeIs('customer.login', 'customer.register') ? 'active' : '' }}">
                            Masuk
                        </a>
                    @endif
                </li>
            </ul>
        </div>
    </div>
</nav>
