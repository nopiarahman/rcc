<div class="" style=" overflow: hidden; position: relative;">
    @php
        // Define theme colors
        $themeColors = [
            'brown' => '#5d4037',
            'yellow' => '#ff8f00',
            'blue' => '#1565c0',
            'orange' => '#ef6c00',
            'green' => '#006a3e'
        ];
        
        // Set default color
        $themeColor = '#006a3e'; // Default to green
        
        // Get theme color if web settings are available
        if (isset($web_settings)) {
            $theme = trim(strtolower($web_settings->theme));
            if (array_key_exists($theme, $themeColors)) {
                $themeColor = $themeColors[$theme];
            }
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
            color: white !important;
        }
        
        /* Theme-specific filled button */
        .btn-theme {
            background-color: {{ $themeColor }} !important;
            border-color: {{ $themeColor }} !important;
            color: white !important;
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
    <img 
        wire:ignore.self 
        src="{{ $minuman->getFirstMediaUrl('foto') }}" 
        alt="{{ $minuman->nama }}" 
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
                    <h4 class="fw-bold mb-0 text-theme">{{ $minuman->nama }}</h4>
                    @if($minuman->is_habis)
                        <div class="badge bg-danger mt-1">Habis / Out of Stock</div>
                    @endif
                    <small class="text-muted">{{ $minuman->short_description }}</small>
                </div>
            </div>

            {{-- Tags --}}
            <div class="mb-3 d-flex flex-wrap" style="text-indent: 1">
                @foreach ($minuman->bahans->where('kategori', 'display') as $item)
                    <div class="tag" style="font-size: 0.8rem">{{ $item->nama }}</div>
                @endforeach
            </div>

            {{-- About --}}
            <div class="mb-4">
                <h6 class="fw-bold text-theme">Keterangan</h6>
                <div class="text-muted" style="font-size: 0.9rem;">
                    {!! $minuman->deskripsi !!}
                </div>
            </div>
            <div class="d-flex justify-content-center align-items-center">
                <div class="text-center">
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        @if ($minuman->activeDiscount())
                            <div class="d-flex flex-column align-items-center">
                                <div class="text-decoration-line-through text-muted" style="font-size: 1rem;">
                                    Rp {{ number_format($minuman->default_price, 0, ',', '.') }}
                                </div>
                                <div class="text-theme" style="font-size: 1.5rem;">
                                    Rp {{ number_format($minuman->discounted_price, 0, ',', '.') }}
                                </div>
                                <div class="badge bg-danger text-white mb-1" style="font-size: 0.7rem;">
                                    @php
                                        $discount = $minuman->activeDiscount();
                                        $discountText = $discount->name . ' - ' . ($discount->discount_type === 'percentage' 
                                            ? intval($discount->discount_amount) . '%' 
                                            : 'Rp' . number_format($discount->discount_amount, 0, ',', '.'));
                                    @endphp
                                    {{ $discountText }}
                                </div>
                            </div>
                        @else
                            <div class="text-theme" style="font-size: 1.5rem;">
                                Rp {{ number_format($minuman->default_price, 0, ',', '.') }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- DEBUG: Price Calculation -->
                    {{-- <div class="card mt-3 mb-3 border border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">DEBUG: Price Calculation</h5>
                        </div>
                        <div class="card-body">
                            <h6>Base Information:</h6>
                            <ul>
                                <li>Base Price: Rp {{ number_format($minuman->base_price, 0, ',', '.') }}</li>
                                <li>Default Size: {{ $minuman->defaultSize ? $minuman->defaultSize->name . ' (+Rp ' . number_format($minuman->defaultSize->price, 0, ',', '.') . ')' : 'None' }}</li>
                                <li>Default Sugar: {{ $minuman->defaultSugar ? $minuman->defaultSugar->level . ' (+Rp ' . number_format($minuman->defaultSugar->price, 0, ',', '.') . ')' : 'None' }}</li>
                                <li>Default Topping: {{ $minuman->defaultTopping ? $minuman->defaultTopping->nama . ' (+Rp ' . number_format($minuman->defaultTopping->price, 0, ',', '.') . ')' : 'None' }}</li>
                            </ul>
                            
                            <h6>Price Calculation:</h6>
                            <ul>
                                <li>Base Price: Rp {{ number_format($minuman->base_price, 0, ',', '.') }}</li>
                                @php
                                    $sizePrice = $minuman->defaultSize ? $minuman->defaultSize->price : 0;
                                    $sugarPrice = $minuman->defaultSugar ? $minuman->defaultSugar->price : 0;
                                    $toppingPrice = $minuman->defaultTopping ? $minuman->defaultTopping->default_price : 0;
                                    $subtotal = $minuman->base_price + $sizePrice + $sugarPrice + $toppingPrice;
                                @endphp
                                <li>+ Size Price: Rp {{ number_format($sizePrice, 0, ',', '.') }}</li>
                                <li>+ Sugar Price: Rp {{ number_format($sugarPrice, 0, ',', '.') }}</li>
                                <li>+ Topping Price: Rp {{ number_format($toppingPrice, 0, ',', '.') }}</li>
                                <li><strong>Default Total: Rp {{ number_format($subtotal, 0, ',', '.') }}</strong> (Should match: Rp {{ number_format($minuman->default_price, 0, ',', '.') }})</li>
                            </ul>
                            
                            @if ($minuman->activeDiscount())
                                <h6>Discount Calculation:</h6>
                                @php
                                    $discount = $minuman->activeDiscount();
                                    $discountAmount = $discount->discount_type === 'percentage' 
                                        ? ($minuman->default_price * $discount->discount_amount / 100) 
                                        : $discount->discount_amount;
                                    $finalPrice = $minuman->default_price - $discountAmount;
                                @endphp
                                <ul>
                                    <li>Discount Type: {{ $discount->discount_type === 'percentage' ? 'Percentage' : 'Fixed Amount' }}</li>
                                    <li>Discount Value: {{ $discount->discount_type === 'percentage' ? $discount->discount_amount . '%' : 'Rp ' . number_format($discount->discount_amount, 0, ',', '.') }}</li>
                                    <li>Discount Amount: Rp {{ number_format($discountAmount, 0, ',', '.') }}</li>
                                    <li><strong>Final Price: Rp {{ number_format($finalPrice, 0, ',', '.') }}</strong> (Should match: Rp {{ number_format($minuman->discounted_price, 0, ',', '.') }})</li>
                                </ul>
                            @endif
                        </div>
                    </div> --}}
                    @if($minuman->is_habis)
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

    <!-- Modal Pilihan -->
    <div wire:ignore.self class="modal fade" id="pilihanModal" tabindex="-1" aria-labelledby="pilihanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-theme" id="pilihanModalLabel">Pilih Varian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    {{-- Size --}}
                    @if ($sizes->isNotEmpty())
                        <div class="mb-3">
                            <h6 class="fw-bold text-theme">Ukuran</h6>
                            <div class="btn-group" role="group">
                                @foreach ($sizes as $size)
                                    <input type="radio" wire:model.live="selectedSizeId" class="btn-check" name="size" value="{{ $size->id }}" id="size-{{ $size->id }}" autocomplete="off">
                                    @php
                                        $isSelected = $selectedSizeId == $size->id;
                                    @endphp
                                    <label class="btn m-1 px-3 py-2 position-relative {{ $isSelected ? 'btn-theme' : 'btn-outline-theme' }}" 
                                           for="size-{{ $size->id }}">
                                           {{ $size->name }}
                                        </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Sugar --}}
                    @if ($sugars->isNotEmpty())
                        <div class="mb-3">
                            <h6 class="fw-bold text-theme">Pilihan Gula</h6>
                            <div class="btn-group" role="group">
                                @foreach ($sugars->sortBy('level') as $sugar)
                                    <input type="radio" wire:model.live="selectedSugarId" class="btn-check" name="sugar" value="{{ $sugar->id }}" id="sugar-{{ $sugar->id }}">
                                    @php
                                        $isSelected = $selectedSugarId == $sugar->id;
                                    @endphp
                                    <label class="btn m-1 px-3 py-2 position-relative {{ $isSelected ? 'btn-theme' : 'btn-outline-theme' }}" 
                                           for="sugar-{{ $sugar->id }}">
                                        {{ $sugar->level }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Topping --}}
                    @if ($toppings->isNotEmpty())
                        <div class="mb-3">
                            <h6 class="fw-bold text-theme">Topping</h6>
                            <div class="btn-group" role="group">
                                @foreach ($toppings->sortByDesc('nama') as $topping)
                                    <input type="radio" wire:model.live="selectedToppingId" class="btn-check" name="topping" value="{{ $topping->id }}" id="topping-{{ $topping->id }}">
                                    @php
                                        $isSelected = $selectedToppingId == $topping->id;
                                    @endphp
                                    <label class="btn m-1 px-3 py-2 position-relative {{ $isSelected ? 'btn-theme' : 'btn-outline-theme' }}" 
                                           for="topping-{{ $topping->id }}">
                                        {{ $topping->nama }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- DEBUG: Modal Price Calculation -->
                {{-- <div class="card mt-3 mb-3 border border-danger mx-3">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">DEBUG: Modal Price Calculation</h5>
                    </div>
                    <div class="card-body">
                        <h6>Selected Options:</h6>
                        <ul>
                            <li>Base Price: Rp {{ number_format($minuman->base_price, 0, ',', '.') }}</li>
                            @php
                                $selectedSize = $selectedSizeId ? $minuman->sizes()->find($selectedSizeId) : $minuman->defaultSize;
                                $selectedSugar = $selectedSugarId ? $minuman->sugars()->find($selectedSugarId) : $minuman->defaultSugar;
                                $selectedTopping = $selectedToppingId ? $minuman->toppings()->find($selectedToppingId) : $minuman->defaultTopping;
                                
                                $sizePrice = $selectedSize ? $selectedSize->price : 0;
                                $sugarPrice = $selectedSugar ? $selectedSugar->price : 0;
                                $toppingPrice = $selectedTopping ? $selectedTopping->defau : 0;
                            @endphp
                            <li>Selected Size: {{ $selectedSize ? $selectedSize->name . ' (+Rp ' . number_format($sizePrice, 0, ',', '.') . ')' : 'None' }}</li>
                            <li>Selected Sugar: {{ $selectedSugar ? $selectedSugar->level . ' (+Rp ' . number_format($sugarPrice, 0, ',', '.') . ')' : 'None' }}</li>
                            <li>Selected Topping: {{ $selectedTopping ? $selectedTopping->nama . ' (+Rp ' . number_format($toppingPrice, 0, ',', '.') . ')' : 'None' }}</li>
                        </ul>
                        
                        <h6>Current Total Price Calculation:</h6>
                        <ul>
                            <li>Base Price: Rp {{ number_format($minuman->base_price, 0, ',', '.') }}</li>
                            <li>+ Size Price: Rp {{ number_format($sizePrice, 0, ',', '.') }}</li>
                            <li>+ Sugar Price: Rp {{ number_format($sugarPrice, 0, ',', '.') }}</li>
                            <li>+ Topping Price: Rp {{ number_format($toppingPrice, 0, ',', '.') }}</li>
                            @php
                                $modalSubtotal = $minuman->base_price + $sizePrice + $sugarPrice + $toppingPrice;
                            @endphp
                            <li><strong>Current Total: Rp {{ number_format($modalSubtotal, 0, ',', '.') }}</strong> (Should match: Rp {{ number_format($this->totalPrice, 0, ',', '.') }})</li>
                        </ul>
                        
                        @if ($minuman->activeDiscount())
                            <h6>Discount Applied to Current Selection:</h6>
                            @php
                                $discount = $minuman->activeDiscount();
                                $discountAmount = $discount->discount_type === 'percentage' 
                                    ? ($modalSubtotal * $discount->discount_amount / 100) 
                                    : $discount->discount_amount;
                                $finalModalPrice = $modalSubtotal - $discountAmount;
                            @endphp
                            <ul>
                                <li>Discount Type: {{ $discount->discount_type === 'percentage' ? 'Percentage' : 'Fixed Amount' }}</li>
                                <li>Discount Value: {{ $discount->discount_type === 'percentage' ? $discount->discount_amount . '%' : 'Rp ' . number_format($discount->discount_amount, 0, ',', '.') }}</li>
                                <li>Discount Amount: Rp {{ number_format($discountAmount, 0, ',', '.') }}</li>
                                <li><strong>Final Price with Discount: Rp {{ number_format($finalModalPrice, 0, ',', '.') }}</strong></li>
                            </ul>
                        @endif
                    </div>
                </div> --}}
                
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <div wire:loading.delay.shorter.class="opacity-50 text-yellow">
                        <div class="fw-bold text-theme" style="font-size: 1.25rem;">
                            Rp {{ number_format($this->totalPrice, 0, ',', '.') }}
                        </div>
                    </div>
                    @if($minuman->is_habis)
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
    <x-mobile-nav/>
    
    <!-- Full Size Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body p-0 d-flex justify-content-center position-relative">
                    <!-- Close button as overlay -->
                    <button type="button" class="btn-close position-absolute top-0 end-0 bg-white rounded-circle p-2 m-3 shadow-sm" style="z-index: 1050;" data-bs-dismiss="modal" aria-label="Close"></button>
                    
                    <img 
                        src="{{ $minuman->getFirstMediaUrl('foto') }}" 
                        alt="{{ $minuman->nama }}" 
                        class="img-fluid rounded"
                    >
                </div>
            </div>
        </div>
    </div>
    

</div>