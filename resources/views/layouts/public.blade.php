<!DOCTYPE html>
<html lang="id" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? ($webSettings->site_name ?? 'Raihaan Coffee Corner') }}</title>
    @if($webSettings->favicon_path ?? false)
        <link rel="icon" href="{{ asset('storage/' . $webSettings->favicon_path) }}" sizes="any">
        <link rel="icon" href="{{ asset('storage/' . $webSettings->favicon_path) }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . $webSettings->favicon_path) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.png') }}" sizes="any">
        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset('touch-icon.png') }}">
    @endif

    @livewireStyles
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=home,receipt_long,shopping_bag"/>
    @livewireScripts
    {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=shoopi" /> --}}
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    {{-- @fluxAppearance --}}
    @section('styles')
    <style>
        .mobile-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #fff;
            border-top: 1px solid #ccc;
            z-index: 1030;
        }
        .mobile-nav .nav-item {
            flex: 1;
            text-align: center;
            padding: 10px 0;
            font-weight: 500;
            color: #555;
        }
        .mobile-nav .nav-item:hover {
            background-color: #f8f9fa;
        }
        .mobile-nav .nav-link {
            color: inherit;
            text-decoration: none;
        }
        .fixed-img-height {
            height: 170px;
            object-fit: cover;
        }
        .gradient {
            @if(isset($webSettings) && $webSettings->themeColor)
                background: linear-gradient(to top, {{ $webSettings->themeColor->primary_color }}, {{ $webSettings->themeColor->secondary_color }});
            @else
                background: linear-gradient(to top, #011a0f, #006a3e);
            @endif
            min-height: 100vh;
            transition: background-color 0.3s, background-image 0.3s;
        }
        
        /* Theme color variables */
        :root {
            @if(isset($webSettings) && $webSettings->themeColor)
                --primary-color: {{ $webSettings->themeColor->primary_color }};
                --secondary-color: {{ $webSettings->themeColor->secondary_color }};
                --button-bg-color: {{ $webSettings->themeColor->button_bg_color }};
                --button-text-color: {{ $webSettings->themeColor->button_text_color }};
                --card-bg-color: {{ $webSettings->themeColor->card_bg_color }};
                --text-color: {{ $webSettings->themeColor->text_color }};
                --muted-text-color: {{ $webSettings->themeColor->muted_text_color }};
            @else
                --primary-color: #011a0f;
                --secondary-color: #006a3e;
                --button-bg-color: #006a3e;
                --button-text-color: #ffffff;
                --card-bg-color: #ffffff;
                --text-color: #1a1a1a;
                --muted-text-color: #6b7280;
            @endif
        }
        
        /* Theme-specific styles */
        .btn-primary {
            background-color: var(--button-bg-color);
            color: var(--button-text-color);
            border: 1px solid var(--button-bg-color);
        }
        
        .btn-outline-primary {
            color: var(--button-bg-color);
            border: 1px solid var(--button-bg-color);
            background-color: transparent;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--button-bg-color);
            color: var(--button-text-color);
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .border-primary {
            border-color: var(--primary-color) !important;
        }
        
        .card {
            background-color: var(--card-bg-color);
            color: var(--text-color);
        }
        
        .text-muted {
            color: var(--muted-text-color) !important;
        }
        
        /* Theme-specific button styles */
        .btn-primary {
            @if(isset($webSettings) && $webSettings->theme === 'brown')
                background-color: #5d4037;
                border-color: #5d4037;
            @elseif(isset($webSettings) && $webSettings->theme === 'yellow')
                background-color: #ff8f00;
                border-color: #ff8f00;
            @elseif(isset($webSettings) && $webSettings->theme === 'blue')
                background-color: #1565c0;
                border-color: #1565c0;
            @elseif(isset($webSettings) && $webSettings->theme === 'orange')
                background-color: #ef6c00;
                border-color: #ef6c00;
            @else
                background-color: #006a3e;
                border-color: #006a3e;
            @endif
        }
        
        .btn-primary:hover {
            @if(isset($webSettings) && $webSettings->theme === 'brown')
                background-color: #3e2723;
                border-color: #3e2723;
            @elseif(isset($webSettings) && $webSettings->theme === 'yellow')
                background-color: #ff6f00;
                border-color: #ff6f00;
            @elseif(isset($webSettings) && $webSettings->theme === 'blue')
                background-color: #0d47a1;
                border-color: #0d47a1;
            @elseif(isset($webSettings) && $webSettings->theme === 'orange')
                background-color: #e65100;
                border-color: #e65100;
            @else
                background-color: #004d29;
                border-color: #004d29;
            @endif
        }

        </style>
</head>
<body>
    <x-web-nav />
    <div class="gradient pb-n3">
        {{ $slot }}
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <x-mobile-nav />
    
</body>
</html>
