<div>
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
        /* .text-muted-theme {
            color: {{ $mutedTextColor }} !important;
        } */
        
        /* .text-theme-primary {
            color: {{ $themeTextColor }} !important;
        }
         */
        .btn-theme:hover {
            opacity: 0.9;
            background-color: {{ $themeColor }} !important;
            border-color: {{ $themeColor }} !important;
        }
    </style>
    
    {{-- Kategori --}}
    <h5 class="fw-bold mb-2 text-theme">Minuman {{$filterKategori }}</h5>
    <div class="d-flex gap-2 overflow-auto pb-2">
        
        <button 
            wire:click="gantiKategori('')" 
            class="btn rounded-4 btn-sm px-3 {{ $filterKategori == '' ? 'btn-theme' : 'btn-outline-secondary' }}">
            Semua
        </button>
        @foreach($allKategoris as $kategori)
        <button 
            wire:click="gantiKategori('{{ $kategori }}')" 
            class="btn rounded-4 btn-sm px-3 {{ $filterKategori == $kategori ? 'btn-theme' : 'btn-outline-secondary' }}">
            {{ $kategori }}
        </button>
        @endforeach
    </div>


    
    {{-- Daftar Minuman --}}
    <div>
        @php
            $collections = $filterKategori == ''
                ? $minumans->groupBy('kategori')->sortKeys()
                : collect([$filterKategori => $minumans->where('kategori', $filterKategori)]);
        @endphp
    
        @forelse ($collections as $kategori => $items)
            <div class="row g-3 mt-1">
                <h5 class="fw-bold text-theme" style="margin-bottom: -0.5rem">{{ $kategori }} Series</h5>
                @forelse ($items as $item)
                    <div class="col-6">
                        <a wire:navigate href="{{ route('minuman.detail', $item->id) }}" class="text-decoration-none text-dark">
                            <div class="card border-1 rounded-4 position-relative">
                                <!-- Discount Badge -->
                                @if($item->activeDiscount())
                                    @php
                                        $discount = $item->activeDiscount();
                                        $discountText = $discount->discount_type === 'percentage' 
                                            ? 'Diskon ' . intval($discount->discount_amount) . '%' 
                                            : 'Diskon Rp' . number_format($discount->discount_amount, 0, ',', '.');
                                    @endphp
                                    <div class="position-absolute top-0 end-0 m-2 z-index-1">
                                        <span class="badge bg-danger text-white">
                                            {{ $discountText }}
                                        </span>
                                    </div>
                                @endif
                                
                                <!-- Out of Stock Overlay -->
                                @if($item->is_habis)
                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background-color: rgba(0,0,0,0.6); z-index: 2; border-radius: 0.8rem;">
                                        <div class="badge bg-secondary p-2" style="transform:font-size: 1rem;">
                                            <i class="bi bi-x-circle me-1"></i> Habis
                                        </div>
                                    </div>
                                @endif
                                
                                <img src="{{ $item->getFirstMediaUrl('foto') ?: asset('images/no-image.png') }}"
                                     class="card-img-top object-fit-cover fixed-img-height rounded-4 p-2"
                                     alt="{{ $item->nama }}">
                                <div class="card-body p-2">
                                    <div class="fw-bold small" style="min-height: 2.5rem; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $item->nama }}
                                    </div>
                                    <div class="small">
                                        @if($item->activeDiscount())
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="text-decoration-line-through text-muted" style="font-size: 0.75rem;">
                                                    Rp {{ number_format($item->default_price, 0, ',', '.') }}
                                                </span>
                                                <span class="text-danger fw-bold">
                                                    Rp {{ number_format($item->discounted_price, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        @else
                                            <div class="text-muted">
                                                Rp {{ number_format($item->default_price, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <small class="text-muted">Belum ada minuman untuk kategori ini.</small>
                    </div>
                @endforelse
            </div>
        @empty
            <div class="col-12 text-center">
                <small class="text-muted">Belum ada minuman untuk kategori ini.</small>
            </div>
        @endforelse
    </div>

    @if($makanans->count())
    {{-- Daftar Makanan --}}
    <div class="mt-4">
        <h5 class="fw-bold mb-2 text-theme">Makanan {{$filterKategori }}</h5>
        @php
            $collectionsMakanan = $filterKategori == ''
                ? $makanans->groupBy('kategori')->sortKeys()
                : collect([$filterKategori => $makanans->where('kategori', $filterKategori)]);
        @endphp
        @forelse ($collectionsMakanan as $kategori => $items)
            <div class="row g-3 mt-1">
                <h5 class="fw-bold text-theme" style="margin-bottom: -0.5rem">{{ $kategori }}</h5>
                @forelse ($items as $item)
                    <div class="col-6">
                        <a wire:navigate href="{{ route('makanan.detail', $item->id) }}" class="text-decoration-none text-dark">
                            <div class="card border-1 rounded-4 position-relative">
                                @if($item->is_habis)
                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background-color: rgba(0,0,0,0.6); z-index: 2; border-radius: 0.8rem;">
                                        <div class="badge bg-secondary p-2" style="transform:font-size: 1rem;">
                                            <i class="bi bi-x-circle me-1"></i> Habis
                                        </div>
                                    </div>
                                @endif
                                <img src="{{ $item->getFirstMediaUrl('gambar') ?: asset('images/no-image.png') }}"
                                     class="card-img-top object-fit-cover fixed-img-height rounded-4 p-2"
                                     alt="{{ $item->nama }}">
                                <div class="card-body p-2">
                                    <div class="fw-bold small" style="min-height: 2.5rem; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $item->nama }}
                                    </div>
                                    <div class="small">
                                        <div class="text-muted">
                                            Rp {{ number_format($item->base_price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <small class="text-muted">Belum ada makanan untuk kategori ini.</small>
                    </div>
                @endforelse
            </div>
        @empty
            <div class="col-12 text-center">
                <small class="text-muted">Belum ada makanan untuk kategori ini.</small>
            </div>
        @endforelse
    </div>
    @endif

    @if($botolans->count())
    {{-- CTA Botolan --}}
    <div class="mt-4">
        <h5 class="fw-bold mb-2 text-theme">Botolan</h5>
        @php
            $ctaFoto = $web_settings?->botolan_placeholder_path
                ? asset('storage/' . $web_settings->botolan_placeholder_path)
                : $botolans->first()?->getFirstMediaUrl('foto');
        @endphp
        <a wire:navigate href="{{ route('botolan.page') }}" class="text-decoration-none">
            <div class="rounded-4 position-relative d-flex align-items-center"
                 style="min-height:148px; background-color:{{ $themeColor }}; overflow:hidden;">

                {{-- Dekorasi lingkaran latar --}}
                <div style="position:absolute; right:-24px; top:-28px; width:160px; height:160px;
                            border-radius:50%; background:rgba(255,255,255,0.1); pointer-events:none;"></div>
                <div style="position:absolute; right:80px; bottom:-36px; width:110px; height:110px;
                            border-radius:50%; background:rgba(255,255,255,0.06); pointer-events:none;"></div>

                {{-- Foto botolan (PNG transparan) --}}
                @if($ctaFoto)
                    <img src="{{ $ctaFoto }}"
                         style="position:absolute; right:0; top:50%; transform:translateY(-50%);
                                height:140%; max-height:200px; width:44%;
                                object-fit:contain; object-position:right center;
                                filter:drop-shadow(-4px 4px 8px rgba(0,0,0,0.25));">
                @endif

                {{-- Teks CTA --}}
                <div class="ps-4 pe-2 py-4 position-relative" style="z-index:2; max-width:60%;">
                    <div class="fw-bold text-white lh-sm mb-1" style="font-size:1.05rem; letter-spacing:-0.01em;">
                        Minuman Favorit<br>dalam Botol
                    </div>
                    <div class="text-white mb-3" style="font-size:0.72rem; opacity:0.8;">
                        Tersedia berbagai ukuran
                    </div>
                    <span class="btn btn-light btn-sm rounded-3 fw-semibold px-3" style="font-size:0.78rem;">
                        Pesan Sekarang <i class="bi bi-arrow-right ms-1"></i>
                    </span>
                </div>
            </div>
        </a>
    </div>
    @endif

</div>
