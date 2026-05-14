<div style="overflow: hidden; position: relative;">
    @php
        $themeColor = '#4a5d4a';
        $buttonTextColor = '#ffffff';
        $mutedTextColor = '#6b7280';
        if (isset($web_settings) && $web_settings->themeColor) {
            $themeColor = $web_settings->themeColor->button_bg_color;
            $buttonTextColor = $web_settings->themeColor->button_text_color;
            $mutedTextColor = $web_settings->themeColor->muted_text_color;
        }
    @endphp
    <style>
        .btn-theme { background-color: {{ $themeColor }} !important; border-color: {{ $themeColor }} !important; color: {{ $buttonTextColor }} !important; }
        .btn-theme:hover { opacity: 0.9; }
        .btn-theme:disabled { opacity: 0.5; }
        .text-theme { color: {{ $themeColor }} !important; }
        .ukuran-btn.active { background-color: {{ $themeColor }} !important; color: {{ $buttonTextColor }} !important; border-color: {{ $themeColor }} !important; }
        .content-wrapper { margin-top: -2rem; z-index: 2; position: relative; background: white; border-radius: 1.5rem 1.5rem 0 0; box-shadow: 0 -4px 6px -1px rgba(0,0,0,0.1); padding-bottom: 5rem; }
        .select-minuman { border-radius: 12px !important; font-size: 0.9rem; border-color: #e5e7eb !important; }
        .select-minuman:focus { border-color: {{ $themeColor }} !important; box-shadow: 0 0 0 0.2rem {{ $themeColor }}33 !important; }
    </style>

    @php
        $headerFotoUrl = null;
        $headerAlt     = 'Botolan';
        $isPlaceholder = false;

        if ($selectedUkuran && $selectedUkuran->getFirstMediaUrl('foto')) {
            $headerFotoUrl = $selectedUkuran->getFirstMediaUrl('foto');
            $headerAlt     = $selectedProduk?->minuman?->nama . ' ' . $selectedUkuran->label;
        } elseif ($selectedProduk && $selectedProduk->getFirstMediaUrl('foto')) {
            $headerFotoUrl = $selectedProduk->getFirstMediaUrl('foto');
            $headerAlt     = $selectedProduk->minuman?->nama;
        } elseif ($web_settings?->botolan_placeholder_path) {
            $headerFotoUrl = asset('storage/' . $web_settings->botolan_placeholder_path);
            $isPlaceholder = true;
        }
    @endphp

    {{-- Header Foto --}}
    <div style="height: 300px; background: #f3f4f6; position: relative; overflow: hidden;">
        @if($headerFotoUrl)
            @if(!$isPlaceholder)
                <img src="{{ $headerFotoUrl }}"
                     alt="{{ $headerAlt }}"
                     data-bs-toggle="modal" data-bs-target="#fotoModal"
                     style="width:100%; height:100%; object-fit:cover; object-position:center; cursor:zoom-in;">
                <div style="position:absolute; bottom:0.75rem; right:0.75rem; z-index:5;">
                    <span class="badge bg-dark bg-opacity-50 rounded-pill px-2 py-1" style="font-size:0.7rem; cursor:pointer;"
                          data-bs-toggle="modal" data-bs-target="#fotoModal">
                        <i class="bi bi-arrows-fullscreen me-1"></i>Lihat penuh
                    </span>
                </div>
            @else
                <img src="{{ $headerFotoUrl }}"
                     alt="Botolan"
                     style="width:100%; height:100%; object-fit:cover; object-position:center; opacity:0.85;">
                <div class="d-flex align-items-end justify-content-start h-100 pb-3 ps-3"
                     style="position:absolute; top:0; left:0; width:100%;
                            background:linear-gradient(to top, rgba(0,0,0,0.45) 0%, transparent 60%);">
                    <small class="text-white fw-semibold" style="font-size:0.8rem; opacity:0.9;">Pilih minuman untuk melihat foto botolan</small>
                </div>
            @endif
        @else
            <div class="d-flex align-items-center justify-content-center h-100 flex-column gap-2">
                <i class="bi bi-bag" style="font-size:4rem; color:#d1d5db;"></i>
                <small style="color:#9ca3af; font-size:0.8rem;">Pilih minuman untuk melihat foto</small>
            </div>
        @endif

        <a wire:navigate href="{{ route('home') }}"
           class="btn btn-light rounded-circle shadow-sm"
           style="position:absolute; top:1rem; left:1rem; width:38px; height:38px; display:flex; align-items:center; justify-content:center; z-index:10;">
            <i class="fas fa-chevron-left"></i>
        </a>
    </div>

    {{-- Lightbox Modal --}}
    @if($headerFotoUrl && !$isPlaceholder)
    <div class="modal fade" id="fotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:100vw; margin:0; padding:0;">
            <div class="modal-content border-0" style="background:rgba(0,0,0,0.92); min-height:100dvh; border-radius:0;">
                <div class="modal-body d-flex align-items-center justify-content-center p-0"
                     data-bs-dismiss="modal"
                     style="min-height:100dvh; cursor:zoom-out;">
                    <button type="button" class="btn btn-light rounded-circle"
                            data-bs-dismiss="modal"
                            onclick="event.stopPropagation()"
                            style="position:absolute; top:1rem; right:1rem; width:38px; height:38px; display:flex; align-items:center; justify-content:center; z-index:10; color:#111 !important;">
                        X
                    </button>
                    <img src="{{ $headerFotoUrl }}"
                         alt="{{ $headerAlt }}"
                         onclick="event.stopPropagation()"
                         style="max-width:100%; max-height:100dvh; object-fit:contain; cursor:default;"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Konten --}}
    <div class="content-wrapper px-4 pt-4">

        @if(session('success'))
            <div class="alert alert-success py-2 mb-3" style="font-size:0.85rem;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger py-2 mb-3" style="font-size:0.85rem;">{{ session('error') }}</div>
        @endif

        <h5 class="fw-bold mb-1" style="font-size:1.2rem;">Pesan Minuman Botolan</h5>
        <p class="text-muted mb-4" style="font-size:0.83rem;">Pilih minuman favorit kamu dalam kemasan botol</p>

        {{-- Pilih Minuman --}}
        <div class="mb-4">
            <p class="fw-semibold mb-2" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.05em; color:{{ $mutedTextColor }};">Pilih Minuman</p>
            <select wire:model.live="selectedProdukId" class="form-select select-minuman">
                <option value="">— Pilih minuman —</option>
                @foreach($availableProduks as $produk)
                    <option value="{{ $produk->id }}">{{ $produk->minuman?->nama }}</option>
                @endforeach
            </select>
        </div>

        {{-- Pilih Ukuran --}}
        @if($selectedProduk && $selectedProduk->ukurans->isNotEmpty())
        <div class="mb-4">
            <p class="fw-semibold mb-2" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.05em; color:{{ $mutedTextColor }};">Pilih Ukuran</p>
            <div class="d-flex flex-wrap gap-2">
                @foreach($selectedProduk->ukurans as $ukuran)
                    <button wire:click="$set('selectedUkuranId', {{ $ukuran->id }})"
                        class="btn btn-outline-secondary ukuran-btn {{ $selectedUkuranId == $ukuran->id ? 'active' : '' }}"
                        style="border-radius:12px; font-size:0.82rem; padding:.45rem .9rem;">
                        <span class="fw-semibold">{{ $ukuran->label }}</span><br>
                        <small>Rp{{ number_format($ukuran->harga, 0, ',', '.') }}</small>
                    </button>
                @endforeach
            </div>
        </div>
        @elseif($selectedProdukId)
        <div class="mb-4">
            <p class="text-muted" style="font-size:0.85rem;">Belum ada ukuran tersedia untuk minuman ini.</p>
        </div>
        @endif

        {{-- Harga & Tombol --}}
        <div class="d-flex align-items-center justify-content-between py-3 border-top">
            <div>
                <small class="text-muted">Harga</small>
                <div class="fw-bold text-theme" style="font-size:1.3rem;">
                    @if($selectedUkuran)
                        Rp{{ number_format($selectedUkuran->harga, 0, ',', '.') }}
                    @else
                        —
                    @endif
                </div>
            </div>
            <button wire:click="addToCart"
                class="btn btn-theme px-4"
                style="border-radius:12px; font-size:0.9rem;"
                {{ (!$selectedProdukId || !$selectedUkuranId) ? 'disabled' : '' }}>
                <span wire:loading.remove wire:target="addToCart">
                    <i class="bi bi-cart-plus me-1"></i> Tambah ke Keranjang
                </span>
                <span wire:loading wire:target="addToCart">
                    <span class="spinner-border spinner-border-sm me-1"></span> Menambahkan...
                </span>
            </button>
        </div>
    </div>

    <x-mobile-nav />
</div>
