<div class="" style="overflow: hidden; position: relative;">
    @php
        // Theme color logic copied from minuman-detail
        $themeColor = '#4a5d4a';
        $themeTextColor = '#2d2d2d';
        $buttonTextColor = '#ffffff';
        $cardBgColor = '#ffffff';
        $mutedTextColor = '#6b7280';
        if (isset($web_settings) && $web_settings->themeColor) {
            $themeColor = $web_settings->themeColor->button_bg_color;
            $themeTextColor = $web_settings->themeColor->text_color;
            $buttonTextColor = $web_settings->themeColor->button_text_color;
            $cardBgColor = $web_settings->themeColor->card_bg_color;
            $mutedTextColor = $web_settings->themeColor->muted_text_color;
        }
    @endphp
 <style>
    /* Theme-specific text color */
    .text-theme {
        color: {{ $themeColor }} !important;
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
    
    /* Card styling */
    .card-theme {
        background-color: {{ $cardBgColor }} !important;
    }
    
    /* Text colors */
    .text-muted-theme {
        color: {{ $mutedTextColor }} !important;
    }
    
    .text-theme-primary {
        color: {{ $themeTextColor }} !important;
    }
    
    .btn-theme:hover {
        opacity: 0.9;
        background-color: {{ $themeColor }} !important;
        border-color: {{ $themeColor }} !important;
    }
    
        .page-wrapper {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }
        .content-wrapper {
            margin-top: -2rem;
            z-index: 2;
            position: relative;
            background: white;
            border-radius: 1.5rem 1.5rem 0 0;
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
            padding-bottom: 1rem;
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
        /* .scrollable-content small, */
        /* .scrollable-content p, */
        /* .scrollable-content h6, */
        .scrollable-content .tag,
        .scrollable-content .size-btn {
            font-size: 0.85rem !important;
        }

    </style>
    <div class="page-wrapper">
        {{-- Tombol Kembali --}}
        <div class="header-button d-flex justify-content-between align-items-center px-3 py-2">
            <a wire:navigate href="{{ route('home') }}" class="btn btn-light rounded-circle shadow-sm">
                <i class="fas fa-chevron-left"></i>
            </a>
            @if ($makanan->tag)
                <div class="bg-warning text-white px-2 py-1 rounded-3 fw-bold ms-2">
                    {{$makanan->tag}}
                </div>
            @endif
            @if (session()->has('message'))
                <div class="alert alert-success mt-3" style="position: fixed; top:80%;border-radius:20px">
                    {{ session('message') }}
                </div>
            @endif
        </div>
        {{-- Gambar --}}
        <img 
            wire:ignore.self 
            src="{{ $makanan->getFirstMediaUrl('foto') }}" 
            alt="{{ $makanan->nama }}" 
            class="sticky-image" 
            style="cursor: pointer;" 
            data-bs-toggle="modal" 
            data-bs-target="#imageModal"
        >

        {{-- Konten --}}
        <div wire:ignore.self class="card rounded-top-xl content-wrapper shadow-lg" style="animation: fadeSlideUp 0.6s ease-out both; padding-bottom: 5rem">
            <div style="padding: 1.5rem 1.5rem 1rem;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold mb-0 text-theme">{{ $makanan->nama }}</h4>
                        @if($makanan->is_habis)
                            <div class="badge bg-danger mt-1">Habis / Out of Stock</div>
                        @endif
                        <small class="text-muted">{{ $makanan->short_description }}</small>
                    </div>
                </div>

                {{-- Tags Bahan --}}
                <div class="mb-3 d-flex flex-wrap" style="text-indent: 1">
                    @foreach ($makanan->bahans as $item)
                        <div class="tag" style="font-size: 0.8rem">{{ $item->nama }}</div>
                    @endforeach
                </div>

                {{-- About --}}
                <div class="mb-4">
                    <h6 class="fw-bold text-theme">Keterangan</h6>
                    <div class="text-muted" style="font-size: 0.9rem;">
                        {!! $makanan->deskripsi !!}
                    </div>
                </div>

                {{-- Topping Picker
                @if($toppings && count($toppings))
                <div class="mb-4">
                    <h6 class="fw-bold text-theme">Pilih Topping</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($toppings as $topping)
                            <button type="button" wire:click="$set('selectedToppingId', {{ $topping->id }})" class="size-btn {{ $selectedToppingId == $topping->id ? 'active' : 'inactive' }}">
                                {{ $topping->nama }}
                                @if($topping->pivot && $topping->pivot->extra_price)
                                    <span class="badge bg-success ms-2">+Rp {{ number_format($topping->pivot->extra_price, 0, ',', '.') }}</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif --}}
                {{-- Harga & Diskon --}}
                <div class="d-flex justify-content-center align-items-center mb-3">
                    @if ($makanan->activeDiscount())
                        <div class="d-flex flex-column align-items-center">
                            <div class="text-decoration-line-through text-muted" style="font-size: 1rem;">
                                Rp {{ number_format($makanan->base_price, 0, ',', '.') }}
                            </div>
                            <div class="text-theme fw-bold" style="font-size: 1.5rem;">
                                Rp {{ number_format($this->totalPrice, 0, ',', '.') }}
                            </div>
                            <div class="badge bg-danger text-white mb-1 " style="font-size: 0.7rem;">
                                @php
                                    $discount = $makanan->activeDiscount();
                                    $discountText = $discount->name . ' - ' . ($discount->discount_type === 'percentage' 
                                        ? intval($discount->discount_amount) . '%' 
                                        : 'Rp' . number_format($discount->discount_amount, 0, ',', '.'));
                                @endphp
                                {{ $discountText }}
                            </div>
                        </div>
                    @else
                        <div class="fw-bold text-theme" style="font-size: 1.5rem;">
                            Rp {{ number_format($this->totalPrice, 0, ',', '.') }}
                        </div>
                    @endif
                </div>

                {{-- Add to Cart Button --}}
                <div class="d-flex justify-content-center align-items-center">
                    <div class="text-center">
                        @if($makanan->is_habis)
                            <button type="button" class="btn btn-secondary rounded-pill fw-semibold text-white px-4 py-2" disabled>
                                Habis / Out of Stock
                            </button>
                        @else
                            <button type="button" class="btn btn-theme btn-sm rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2 mx-auto" data-bs-toggle="modal" data-bs-target="#pilihanModal">
                                <i class="material-symbols-outlined" style="font-size: 18px; font-variation-settings: 'FILL' 1, 'wght' 500, 'GRAD' 0, 'opsz' 24;">
                                    shopping_bag
                                </i>
                                <span>Masukkan ke Keranjang</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Pilihan -->
    <div wire:ignore.self class="modal fade" id="pilihanModal" tabindex="-1" aria-labelledby="pilihanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-theme" id="pilihanModalLabel">Pilih Varian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    {{-- Topping Picker --}}
                    @if($toppings && count($toppings))
                        <div class="mb-4">
                            <h6 class="fw-bold text-theme">Pilih Topping</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($toppings as $topping)
                                    <button type="button" 
                                            wire:click="$set('selectedToppingId', {{ $topping->id }})" 
                                            class="size-btn {{ $selectedToppingId == $topping->id ? 'active' : 'inactive' }}">
                                        {{ $topping->nama }}
                                        {{-- @if($topping->pivot && $topping->default_price)
                                            <span class="badge bg-success ms-2">+Rp {{ number_format($topping->default_price, 0, ',', '.') }}</span>
                                        @endif --}}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <div wire:loading.delay.shorter.class="opacity-50 text-yellow">
                        <div class="fw-bold text-theme" style="font-size: 1.25rem;font-weight:bolder">
                            Rp {{ number_format($this->totalPrice, 0, ',', '.') }}
                        </div>
                    </div>
                    @if($makanan->is_habis)
                        <button type="button" class="btn btn-secondary rounded-pill fw-semibold text-white px-4 py-2" disabled>
                            Habis / Out of Stock
                        </button>
                    @else
                        <button type="button" class="btn btn-theme rounded-pill fw-semibold text-white px-4 py-2" wire:click="addToCart" data-bs-dismiss="modal">
                            Masuk Keranjang
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Full Size Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body p-0 d-flex justify-content-center position-relative">
                    <!-- Close button as overlay -->
                    <button type="button" class="btn-close position-absolute top-0 end-0 bg-white rounded-circle p-2 m-3 shadow-sm" style="z-index: 1050;" data-bs-dismiss="modal" aria-label="Close"></button>
                    
                    <img 
                        src="{{ $makanan->getFirstMediaUrl('foto') }}" 
                        alt="{{ $makanan->nama }}" 
                        class="img-fluid rounded"
                    >
                </div>
            </div>
        </div>
    </div>
    
</div>
