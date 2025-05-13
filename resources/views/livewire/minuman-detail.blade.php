<div class="" style="min-height: 100vh; overflow: hidden; position: relative; margin-bottom:-2rem;">
        <style>
            .page-wrapper {
                display: flex;
                flex-direction: column;
                height: 100vh;
                overflow: hidden;
                position: relative;
            }
            .content-wrapper {
                flex: 1;
                overflow: hidden;
                margin-top: -4rem;
                z-index: 2;
                position: relative;
            }
            .rounded-top-xl {
                border-top-left-radius: 2rem;
                border-top-right-radius: 2rem;
            }
            .tag {
                background-color: #f4f4f4;
                border-radius: 9999px;
                padding: 4px 12px;
                font-style: italic;
                color: #333;
                margin-right: 6px;
                margin-bottom: 4px;
            }
            .size-btn {
                border-radius: 9999px;
                padding: 6px 20px;
                border: none;
                font-weight: 600;
                margin-right: 8px;
                cursor: pointer;
            }
            .size-btn.active {
                background-color: #006a3e;
                color: white;
            }
            .size-btn.inactive {
                background-color: #f2f2f2;
                color: #333;
            }
            .sticky-image {
                width: 100%;
                height: 250px;
                object-fit: cover;
                position: relative;
                z-index: 1;
                -webkit-mask-image: linear-gradient(to bottom, black 70%, transparent 100%);
                mask-image: linear-gradient(to bottom, black 75%, transparent 100%);
                animation: fadeInImage 0.8s ease-out forwards;
            }
            .content-scrollable {
                position: absolute;
                z-index: 20;
                overflow-y: auto;
                display: flex;
                flex-direction: column;
            }
            .scrollable-content {

                overflow-y: auto;
                margin-top: 2rem;
                padding:0 2rem 2rem 2rem;
            }
            .scroll-inner {
                scrollbar-width: thin;
                scrollbar-color: #ccc transparent;
            }
            .scroll-inner::-webkit-scrollbar {
                width: 6px;
            }
            .scroll-inner::-webkit-scrollbar-thumb {
                background-color: #ccc;
                border-radius: 10px;
            }
            .header-button {
                position: fixed;
                top: 16px;
                left: 16px;
                z-index: 30;
            }
            .add-to-cart {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 25;
                margin: 0 1rem 0 1rem;
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
            @keyframes fadeInImage {
                from {
                    opacity: 0;
                    transform: scale(1.05); /* opsional: efek zoom-in halus */
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }
            .content-scrollable {
                animation: fadeSlideUp 0.6s ease-out both;
            }
            .header-button {
                position: absolute;
                top: 20px;
                left: 0;
                right: 0;
                z-index: 10;
            }

            .header-title {
                font-size: 1.2rem;
            }
            /* Ukuran font dalam konten scrollable */
            .scrollable-content small,
            .scrollable-content p,
            .scrollable-content h6,
            .scrollable-content .tag,
            .scrollable-content .size-btn {
                font-size: 0.85rem !important;
            }

            /* Ukuran tombol lebih kecil */
            .scrollable-content .size-btn {
                padding: 4px 14px;
                font-weight: 500;
            }

            /* Heading size disesuaikan */
            .scrollable-content h4 {
                font-size: 1rem;
            }
            .scrollable-content h6 {
                font-size: 0.9rem;
            }
        </style>
    
    <div class="page-wrapper">
        {{-- Tombol Kembali --}}
        <div class="header-button d-flex justify-content-between align-items-center px-3 py-2">
            <a wire:navigate href="{{ route('home') }}" class="btn btn-light rounded-circle shadow-sm">
                <i class="fas fa-chevron-left"></i>
            </a>
            @if ($minuman->tag)
                <div class="bg-warning text-white px-2 py-1 rounded-3 fw-bold ms-2">
                    {{$minuman->tag}}
                </div>
            @endif
            @if (session()->has('message'))
                    <div class="alert alert-success mt-3" style="position: fixed; top:80%;border-radius:20px">
                        {{ session('message') }}
                    </div>
                @endif
        </div>
        {{-- Gambar --}}
        <img src="{{ $minuman->getFirstMediaUrl('foto') }}" alt="{{ $minuman->nama }}" class="sticky-image">
    
    
        {{-- Konten Scrollable --}}
        <div class="card rounded-top-xl content-wrapper shadow-lg" style="animation: fadeSlideUp 0.6s ease-out both;padding-bottom:2rem">
            <div class="scrollable-content" style="padding-bottom: 1rem; margin-bottom:2rem">

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold mb-0">{{ $minuman->nama }}</h4>
                        <small class="text-muted">{{ $minuman->short_description }}</small>
                    </div>
                </div>

                {{-- Tags --}}
                <div class="mb-3 d-flex flex-wrap" style="text-indent: 1">
                    @foreach ($minuman->bahans as $item)
                        @if ($item->nama != 'Gelas + Straw')
                            <div class="tag">{{ $item->nama }}</div>
                        @endif
                    @endforeach
                </div>

                {{-- Size --}}
                @if ($minuman->sizes->isNotEmpty())
                    <div class="mb-3">
                        <h6 class="fw-bold">Ukuran</h6>
                        <div class="d-flex flex-wrap">
                            @foreach ($minuman->sizes as $size)
                                <button type="button"
                                    wire:click="$set('selectedSizeId', {{ $size->id }})"
                                    class="size-btn {{ $selectedSizeId == $size->id ? 'active' : 'inactive' }}">
                                    {{ $size->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Sugar --}}
                @if ($minuman->sugars->isNotEmpty())
                    <div class="mb-3">
                        <h6 class="fw-bold">Pilihan Gula</h6>
                        <div class="d-flex flex-wrap">
                            @foreach ($minuman->sugars->sortBy('level') as $sugar)
                                <button type="button"
                                    wire:click="$set('selectedSugarId', {{ $sugar->id }})"
                                    class="size-btn {{ $selectedSugarId == $sugar->id ? 'active' : 'inactive' }}">
                                    {{ $sugar->level }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Topping --}}
                @if ($minuman->toppings->isNotEmpty())
                    <div class="mb-3">
                        <h6 class="fw-bold">Topping</h6>
                        <div class="d-flex flex-wrap">
                            @foreach ($minuman->toppings->sortByDesc('nama') as $topping)
                                <button type="button"
                                    wire:click="$set('selectedToppingId', {{ $topping->id }})"
                                    class="size-btn {{ $selectedToppingId == $topping->id ? 'active' : 'inactive' }}">
                                    {{ $topping->nama }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- About --}}
                <div class="mb-4">
                    <h6 class="fw-bold">Keterangan</h6>
                    <p class="text-muted" style="font-size: 0.9rem;">
                        {{ $minuman->deskripsi }}
                    </p>
                </div>
                {{-- Add to Cart (Inside Scrollable) --}}
                <div class="add-to-cart-inside d-flex justify-content-between align-items-center rounded-3">
                    <button wire:click="addToCart"
                        class="btn btn-success btn-sm rounded-pill py-1 fw-semibold">
                        Masuk Keranjang
                    </button>
                    <div class="fw-bold text-dark mb-0" style="font-size: 1.5rem;">
                        Rp {{ number_format($this->calculateTotalPrice(), 0, ',', '.') }}
                    </div>
                </div>
            </div>

            {{-- Add to Cart --}}
            {{-- <div class="add-to-cart p-3 d-flex justify-content-between align-items-center bg-white">
                <button wire:click="addToCart"
                    class="btn btn-success rounded-pill px-4 py-2 fw-semibold">
                    Tambah Ke Keranjang
                </button>
                <div class="fw-bold fs-5 text-dark mb-0">
                    Rp {{ number_format($this->calculateTotalPrice(), 0, ',', '.') }}
                </div>
            </div> --}}
        </div>
    <x-mobile-nav/>

</div>