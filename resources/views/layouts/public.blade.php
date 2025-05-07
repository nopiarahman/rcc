<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Raihaan Coffee Corner' }}</title>
    <link rel="icon" href="{{asset('favicon.png')}}" sizes="any">
    <link rel="icon" href="{{asset('favicon.png')}}" type="image/svg+xml">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=home,receipt_long,shopping_bag"/>
    {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=shoopi" /> --}}
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance

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
    </style>
</head>
<body>
    <div class="container py-3">
        @yield('content')
    </div>

    @livewireScripts
</body>
</html>
