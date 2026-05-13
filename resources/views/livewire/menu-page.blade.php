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
    @endphp
    <style>
    @if(isset($web_settings->themeColor) && $web_settings->themeColor->name == 'green')
        .text-theme-primary {
            color: #ffffff !important;
        }
        .text-muted-theme {
            color: #ffffff !important;
        }
    @else
        .text-theme-primary {
            color: {{ $themeTextColor }} !important;
        }
        .text-muted-theme {
            color: {{ $mutedTextColor }} !important;
        }
    @endif
    
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
            font-size: 1rem; /* Ukuran teks */
            font-weight: bold;
            color: #333; /* Warna teks */
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
    <div class="logo-container mb-3">
        <img src="{{ asset('storage/' . $webSettings->logo_path) }}" alt="{{ $webSettings->site_name }}" style="width: 30px; height:auto">
        <div class="logo-text text-theme-primary">{{ $webSettings->site_name }}</div>
    </div>
    {{-- Info Pengguna --}}
    <div class="d-flex align-items-center justify-content-between mb-3 ">
        <div>
            @php
                $jam = date('G');
                if ($jam >= 0 && $jam < 11) {
                    $ucapan = 'Selamat pagi!';
                } elseif ($jam >= 11 && $jam < 15) {
                    $ucapan = 'Selamat siang!';
                } elseif ($jam >= 15 && $jam < 18) {
                    $ucapan = 'Selamat sore!';
                } else {
                    $ucapan = 'Selamat malam!';
                }
            @endphp
            <div class="fw-bold text-theme-primary">{{ $ucapan }}</div>
            <small class="text-muted-theme">{{ $webSettings->tagline ?? 'Kopi ala cafe sampai ke pintu rumahmu' }}</small>
        </div>
        <div class="btn-theme rounded-circle p-1 shadow-sm">
            <img src="{{ asset('images/profile-placeholder.png') }}" class="rounded-circle" width="40" height="40" alt="Profile">
        </div>
    </div>
    
    {{-- Hero Section --}}
<div class="container px-0 mb-3">
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
    
    <div class="card" style="padding: 2rem;margin:-2rem;border-radius:2rem 2rem 0 0;margin-top:1rem;margin-bottom:1rem">
        <livewire:daftar-minuman :kategoris="$allKategoris" />
    </div>
    <x-mobile-nav/>

</div>