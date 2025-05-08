<div class="container col-md-6" style="padding: 2rem">
    <style>
        .carousel-image {
            height: 200px; /* Ubah sesuai kebutuhan */
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
        <img src="{{ asset('images/logo-white.png') }}" alt="Logo" style="width: 30px; height:auto">
        <div class="logo-text text-white">Raihaan Coffee Corner</div>
    </div>
    {{-- Info Pengguna --}}
    <div class="d-flex align-items-center justify-content-between mb-3 ">
        <div>
            <div class="fw-bold" style="color: rgb(226, 226, 226)">Selamat datang!</div>
            <small style="color: rgb(175, 175, 175)">Kopi ala cafe sampai ke pintu rumahmu</small>
        </div>
        <div class="bg-success rounded-circle p-1 shadow-sm">
            <img src="{{ asset('images/profile-placeholder.png') }}" class="rounded-circle" width="40" height="40" alt="Profile">
        </div>
    </div>
    
    {{-- Hero Section --}}
<div class="container px-0 mb-3">
    <div class="position-relative">
        <div id="heroCarousel" class="carousel slide rounded-4 overflow-hidden shadow-lg" data-bs-ride="carousel" data-bs-interval="4000">
            {{-- Indicators --}}
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            </div>

            {{-- Slides --}}
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{ asset('images/banner1.jpg') }}" class="d-block w-100 carousel-image" alt="Banner 1">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('images/banner2.jpg') }}" class="d-block w-100 carousel-image" alt="Banner 2">
                </div>
            </div>
        </div>
    </div>
</div>
    
    <div class="card" style="padding: 2rem;margin:-2rem;border-radius:2rem 2rem 0 0;margin-top:1rem;margin-bottom:1rem">
        <livewire:daftar-minuman :kategoris="$allKategoris" />
    </div>
    <x-mobile-nav/>

</div>