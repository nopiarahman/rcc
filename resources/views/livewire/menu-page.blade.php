<div class="container col-md-6" style="padding: 2rem">
    @php
        // Default theme colors if not set
        $themeColor = '#4a5d4a';
        $themeTextColor = '#2d2d2d';
        $buttonTextColor = '#ffffff';
        $cardBgColor = '#ffffff';
        $mutedTextColor = '#6b7280';

        // Get theme colors from database if web settings are available
        if (isset($web_settings) && $web_settings->themeColor) {
            $themeColor = $web_settings->themeColor->button_bg_color;
            $themeTextColor = $web_settings->themeColor->text_color;
            $buttonTextColor = $web_settings->themeColor->button_text_color;
            $cardBgColor = $web_settings->themeColor->card_bg_color;
            $mutedTextColor = $web_settings->themeColor->muted_text_color;
        }

        // Hitung luminance secondary_color (warna atas gradient = background header)
        $bgHex = ltrim(
            (isset($web_settings) && $web_settings->themeColor)
                ? $web_settings->themeColor->secondary_color
                : '#006a3e',
            '#'
        );
        $r = hexdec(substr($bgHex, 0, 2)) / 255;
        $g = hexdec(substr($bgHex, 2, 2)) / 255;
        $b = hexdec(substr($bgHex, 4, 2)) / 255;
        $bgLuminance = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
        $isDarkBg = $bgLuminance < 0.4;
        $headerTextColor  = $isDarkBg ? '#ffffff' : '#1a1a1a';
        $headerMutedColor = $isDarkBg ? 'rgba(255,255,255,0.75)' : '#6b7280';
    @endphp
    <style>
        .text-theme-primary {
            color: {{ $headerTextColor }} !important;
        }
        .text-muted-theme {
            color: {{ $headerMutedColor }} !important;
        }
    
        /* Theme-specific text color */
        .text-theme {
            color: {{ $themeTextColor }} !important;
        }
        
        /* Theme-specific button outline */
        .btn-outline-theme {
            color: {{ $themeColor }} !important;
            border-color: {{ $themeColor }} !important;
        }
        
        .btn-outline-theme:hover {
            background-color: {{ $themeColor }} !important;
            color: {{ $buttonTextColor }} !important;
        }
        
        /* Theme-specific filled button */
        .btn-theme {
            background-color: {{ $themeColor }} !important;
            border-color: {{ $themeColor }} !important;
            color: {{ $buttonTextColor }} !important;
        }
        
        .btn-theme:hover {
            opacity: 0.9;
            background-color: {{ $themeColor }} !important;
            border-color: {{ $themeColor }} !important;
        }
        
        /* Card styling */
        .card-theme {
            background-color: {{ $cardBgColor }} !important;
        }
        
        /* Text colors */
        
        
        
        .carousel-image {
            height: 150px; /* Ubah sesuai kebutuhan */
            object-fit: cover;
            width: 100%;
        }
        /* Area sentuh titik slider diperbesar (border transparan) agar mudah ditekan di HP */
        .carousel-indicators [data-bs-target] {
            border-top-width: 18px;
            border-bottom-width: 18px;
        }

        /* Styling untuk logo dan teks */
        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px; /* Memberikan jarak antara logo dan teks */
        }
        .logo-container img {
            width: 40px; /* Ukuran logo */
            height: 40px;
        }
        .logo-container .logo-text {
            font-size: 1rem;
            font-weight: bold;
            color: {{ $headerTextColor }};
        }
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
        .card{
            animation: fadeSlideUp 0.6s ease-out both;
        }
    </style>

    {{-- Logo dan Teks --}}
    {{-- Di desktop nama toko & logo sudah ada di navbar atas, jadi hanya tampil di HP --}}
    <div class="logo-container mb-3 d-md-none">
        <img src="{{ $webSettings->logo_path ? asset('storage/' . $webSettings->logo_path) : asset('images/logo-white.png') }}" alt="{{ $webSettings->site_name }}" style="width: 30px; height:auto">
        <div class="logo-text text-theme-primary">{{ $webSettings->site_name }}</div>
    </div>
    {{-- Info Pengguna --}}
    <div class="d-flex align-items-center justify-content-between mb-3 ">
        <div>
            @php
                $jam = date('G');
                if ($jam >= 0 && $jam < 11) {
                    $salam = 'Selamat pagi';
                } elseif ($jam >= 11 && $jam < 15) {
                    $salam = 'Selamat siang';
                } elseif ($jam >= 15 && $jam < 18) {
                    $salam = 'Selamat sore';
                } else {
                    $salam = 'Selamat malam';
                }
                $customerLoggedIn = Auth::guard('customer')->user();
                $ucapan = $customerLoggedIn
                    ? $salam . ', ' . $customerLoggedIn->nama . '!'
                    : $salam . '!';
            @endphp
            <div class="fw-bold text-theme-primary">{{ $ucapan }}</div>
            <small class="text-muted-theme">{{ $webSettings->tagline ?? 'Kopi ala cafe sampai ke pintu rumahmu' }}</small>
        </div>
        <div class="btn-theme rounded-circle p-1 shadow-sm">
            <img src="{{ asset('images/profile-placeholder.png') }}" class="rounded-circle" width="40" height="40" alt="Profile">
        </div>
    </div>
    
    {{-- Hero Section --}}
<div class="container px-0 mb-3" style="margin-left:-1rem; margin-right:-1rem; width:auto;">
    <div class="position-relative">
        <div id="heroCarousel" class="carousel slide rounded-4 overflow-hidden shadow-lg" data-bs-ride="carousel" data-bs-interval="2000">
            {{-- Indicators --}}
            <div class="carousel-indicators">
                @foreach($banners as $banner)
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }} opacity-50" aria-current="{{ $loop->first ? 'true' : 'false' }}" aria-label="Slide {{ $loop->index + 1 }}"></button>
                @endforeach
            </div>

            {{-- Slides --}}
            <div class="carousel-inner">
                @foreach($banners as $banner)
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        <img src="{{ $banner->getFirstMediaUrl('banners', 'thumb') }}" class="d-block w-100 carousel-image" alt="{{ $banner->title }}">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
    
    {{-- Banner Botolan --}}
    @if($botolans->count() > 0)
    @php
        $ctaFoto = isset($web_settings) && $web_settings?->botolan_placeholder_path
            ? asset('storage/' . $web_settings->botolan_placeholder_path)
            : $botolans->first()?->getFirstMediaUrl('foto');
    @endphp
    <div class="mb-3" style="margin-left:-1rem; margin-right:-1rem;">
        <a href="{{ route('botolan.page') }}" class="text-decoration-none">
            <div class="rounded-4 position-relative d-flex align-items-center"
                 style="min-height:148px; background-color:#ffffff; overflow:hidden; border: 1px solid #f0f0f0;">

                <div style="position:absolute; right:-24px; top:-28px; width:160px; height:160px;
                            border-radius:50%; background:{{ $themeColor }}1a; pointer-events:none;"></div>
                <div style="position:absolute; right:80px; bottom:-36px; width:110px; height:110px;
                            border-radius:50%; background:{{ $themeColor }}0f; pointer-events:none;"></div>

                @if($ctaFoto)
                    <img src="{{ $ctaFoto }}"
                         style="position:absolute; right:0; top:50%; transform:translateY(-50%);
                                height:140%; max-height:200px; width:44%;
                                object-fit:contain; object-position:right center;
                                filter:drop-shadow(-4px 4px 8px rgba(0,0,0,0.25));">
                @endif

                <div class="ps-4 pe-2 py-4 position-relative" style="z-index:2; max-width:60%;">
                    <div class="fw-bold lh-sm mb-1" style="font-size:1.05rem; letter-spacing:-0.01em; color:{{ $themeColor }};">
                        Minuman Favorit<br>dalam Botol
                    </div>
                    <div class="mb-3" style="font-size:0.72rem; color:#6b7280;">
                        Tersedia berbagai ukuran
                    </div>
                    <span class="btn btn-sm rounded-3 fw-semibold px-3" style="font-size:0.78rem; background-color:{{ $themeColor }}; color:#fff;">
                        Pesan Sekarang <i class="bi bi-arrow-right ms-1"></i>
                    </span>
                </div>
            </div>
        </a>
    </div>
    @endif

    <div class="card" style="padding: 2rem;margin:-2rem;border-radius:2rem 2rem 0 0;margin-top:1rem;margin-bottom:-2rem;padding-bottom:5rem;min-height:60vh">
        <livewire:daftar-minuman :kategoris="$allKategoris" />
    </div>

    @if($popup)
    @php
        $popupKey = 'popup_closed_' . $popup->id . '_' . now()->toDateString();
    @endphp
    <div
        x-data="{ open: false, key: '{{ $popupKey }}' }"
        x-init="open = !localStorage.getItem(key)"
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.55); display:flex; align-items:center; justify-content:center; padding:1rem;"
        x-cloak>
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90"
            style="background:#fff; border-radius:1rem; max-width:360px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,0.3); overflow:hidden; position:relative;">
            <button
                @click="open = false; localStorage.setItem(key, '1')"
                style="position:absolute; top:0.5rem; right:0.6rem; background:rgba(0,0,0,0.15); border:none; border-radius:50%; width:28px; height:28px; cursor:pointer; font-size:1rem; line-height:1; color:#fff; z-index:1;">
                &times;
            </button>
            @if($popup->title)
                <div style="padding:1rem 1.2rem 0.5rem; font-weight:700; font-size:1.05rem; color:#1a1a1a;">
                    {{ $popup->title }}
                </div>
            @endif
            @if($popup->type === 'image' && $popup->hasMedia('popups'))
                <img src="{{ $popup->getFirstMediaUrl('popups') }}" alt="{{ $popup->title }}" style="width:100%; display:block; max-height:300px; object-fit:cover;">
            @elseif($popup->type === 'text' && $popup->content)
                <div style="padding:{{ $popup->title ? '0.5rem' : '1.5rem' }} 1.2rem 1.5rem; color:#333; font-size:0.95rem; line-height:1.6; white-space:pre-wrap;">{{ $popup->content }}</div>
            @endif
            <div style="padding:0.75rem 1.2rem; text-align:right;">
                <button
                    @click="open = false; localStorage.setItem(key, '1')"
                    style="background:{{ $themeColor }}; color:#fff; border:none; border-radius:0.5rem; padding:0.4rem 1rem; font-size:0.875rem; cursor:pointer;">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif

</div>