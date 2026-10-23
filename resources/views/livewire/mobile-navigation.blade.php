<div>
    @php
        // Default theme colors if not set
        $themeColor = '#4a5d4a';
        $themeTextColor = '#2d2d2d';
        $buttonTextColor = '#ffffff';
        $cardBgColor = '#ffffff';
        $mutedTextColor = '#6b7280';
        
        // Get theme colors from database if web settings are available
        if (isset($settings) && $settings->themeColor) {
            $themeColor = $settings->themeColor->button_bg_color;
            $themeTextColor = $settings->themeColor->text_color;
            $buttonTextColor = $settings->themeColor->button_text_color;
            $cardBgColor = $settings->themeColor->card_bg_color;
            $mutedTextColor = $settings->themeColor->muted_text_color;
        }
    @endphp
    <style>
        .mobile-nav {
            padding: 6px 0;
            background-color: {{ $cardBgColor }};
            border-top: 1px solid {{ $mutedTextColor }}20;
        }
        .mobile-nav .nav-item {
            padding: 8px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: {{ $themeTextColor }};
        }
        .mobile-nav .nav-item.active {
            color: {{ $themeColor }};
        }
        .mobile-nav .material-symbols-outlined {
            font-size: 26px;
            line-height: 1;
            margin-bottom: 3px;
        }
        .mobile-nav .small {
            font-size: 0.75rem;
            line-height: 1;
            margin: 0;
        }
        .mobile-nav .badge {
            background-color: {{ $themeColor }} !important;
            color: white;
            font-weight: 600;
            font-size: 0.65rem;
            right: 30%;
        }
    </style>

    <nav class="mobile-nav d-flex d-md-none shadow-sm position-fixed bottom-0 start-0 end-0">
        <a href="{{ route('home') }}" wire:navigate class="nav-link nav-item flex-fill text-center {{ request()->routeIs('home') ? 'active' : '' }}">
            <i style="font-variation-settings: 'FILL' 1, 'wght' 700, 'GRAD' 0, 'opsz' 48;" class="material-symbols-outlined">home</i>
            <span class="small">Home</span>
        </a>
        <a href="{{ route('cart') }}" wire:navigate class="nav-link nav-item flex-fill text-center position-relative {{ request()->routeIs('cart') ? 'active' : '' }}">
            <i class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1, 'wght' 700, 'GRAD' 0, 'opsz' 48;">
                shopping_bag
            </i>
            @if ($totalItems > 0)
                <span class="position-absolute top-0 badge rounded-pill">
                    {{ $totalItems }}
                </span>
            @endif
            <span class="small">Keranjang</span>
        </a>
        <a href="{{ route('pesanan') }}" wire:navigate
           class="nav-link nav-item flex-fill text-center {{ request()->routeIs('pesanan', 'pesanan.berhasil') ? 'active' : '' }}">
            <i style="font-variation-settings: 'FILL' 1, 'wght' 700, 'GRAD' 0, 'opsz' 48;" class="material-symbols-outlined">receipt_long</i>
            <span class="small">Pesanan</span>
        </a>
        @if(auth('customer')->check())
        <a href="{{ route('customer.account') }}" wire:navigate
           class="nav-link nav-item flex-fill text-center {{ request()->routeIs('customer.account') ? 'active' : '' }}">
            <i style="font-variation-settings: 'FILL' 1, 'wght' 700, 'GRAD' 0, 'opsz' 48;" class="material-symbols-outlined">person</i>
            <span class="small">Akun</span>
        </a>
        @else
        <a href="{{ route('customer.login') }}" wire:navigate
           class="nav-link nav-item flex-fill text-center {{ request()->routeIs('customer.login', 'customer.register') ? 'active' : '' }}">
            <i style="font-variation-settings: 'FILL' 1, 'wght' 700, 'GRAD' 0, 'opsz' 48;" class="material-symbols-outlined">person</i>
            <span class="small">Akun</span>
        </a>
        @endif
    </nav>
</div>
