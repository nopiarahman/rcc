<div class="container p-4 bg-white" style="min-height: 100vh; padding-bottom: 120px;">
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
        $labelStyle = 'font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em;';
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
        .text-muted-theme {
            color: {{ $mutedTextColor }} !important;
        }

        .text-theme-primary {
            color: {{ $themeTextColor }} !important;
        }

        [x-cloak] { display: none !important; }

        /* Target sentuh minimal 40px agar nyaman ditekan di HP */
        .qty-btn {
            min-width: 40px;
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
            font-size: 1.1rem;
            line-height: 1;
        }
        .qty-value {
            min-width: 36px;
            text-align: center;
            font-weight: 600;
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
    .footer {
                animation: fadeSlideUp 0.6s ease-out both;
            }
    </style>
    {{-- Header (Tetap di Atas) --}}
    <div class="header-button d-flex align-items-center gap-3 pb-3">
        <a wire:navigate href="{{ route('home') }}" class="btn btn-light rounded-circle shadow-sm" aria-label="Kembali ke menu">
            <i class="fas fa-chevron-left"></i>
        </a>
        <h1 class="fw-bold mb-0" style="font-size: 1.1rem;">Keranjang</h1>
    </div>

    @if($removed_notices)
        <div class="alert alert-warning d-flex justify-content-between align-items-start gap-2" role="alert" style="font-size:.85rem;">
            <div>
                <strong>Beberapa item dikeluarkan dari keranjang:</strong>
                <ul class="mb-0 ps-3">
                    @foreach($removed_notices as $notice)
                        <li>{{ $notice }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" wire:click="dismissRemovedNotices" aria-label="Tutup"></button>
        </div>
    @endif

    @if($cartItems->isEmpty())
        {{-- Keranjang kosong: tidak menampilkan form checkout --}}
        <div class="text-center py-5">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                 style="width:80px;height:80px;background:#f1f5f9;color:#94a3b8;font-size:2rem;">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h2 class="fw-bold mb-1" style="font-size:1.1rem;">Keranjang masih kosong</h2>
            <p class="text-muted mb-4" style="font-size:.9rem;">Yuk, pilih minuman atau makanan favoritmu dulu.</p>
            <a href="{{ route('home') }}" wire:navigate class="btn btn-theme px-4" style="border-radius:12px;padding:.65rem 1.5rem;">
                Lihat Menu
            </a>
        </div>
    @else
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="small" style="font-size: 0.85rem;">{{ $summary['qty'] }} item</span>
        <button wire:click="clearCart"
                wire:confirm="Kosongkan semua isi keranjang?"
                class="btn btn-link text-danger text-decoration-none btn-sm">Kosongkan Keranjang</button>
    </div>
    {{-- Cart Items --}}
    @foreach($cartItems as $item)
            <div class="card mb-2" wire:key="cart-{{ $item['key'] }}">
                <div class="card-body d-flex justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center">
                        <img src="{{ $item['image'] ?: asset('images/no-image.png') }}"
                             class="rounded me-3 flex-shrink-0"
                             alt="{{ $item['name'] }}"
                             width="60" height="60"
                             style="object-fit: cover; object-position: center; background:#f1f5f9;">
                        <div class="text-muted" style="font-size: 9pt">
                            <h2 class="mb-1 fw-bold text-theme" style="font-size: 0.9rem;">{{ $item['name'] }}</h2>
                            @php $opsi = array_filter([
                                $item['size'] ? 'Size: ' . $item['size'] : null,
                                $item['sugar'] ? 'Gula: ' . $item['sugar'] : null,
                                $item['topping'] ? 'Topping: ' . $item['topping'] : null,
                                $item['ukuran'],
                            ]); @endphp
                            @if($opsi)
                                <small class="d-block">{{ implode(' | ', $opsi) }}</small>
                            @endif
                            @if($item['discount_info'])
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <small class="text-decoration-line-through text-muted">Rp{{ number_format($item['regular_price'], 0, ',', '.') }}</small>
                                    <small class="text-danger fw-bold">Rp{{ number_format($item['unit_price'], 0, ',', '.') }}</small>
                                    <small class="badge bg-danger text-white" style="font-size: 0.7rem;">{{ $item['discount_info']['discount_text'] }}</small>
                                </div>
                            @else
                                <small class="text-muted">Rp{{ number_format($item['unit_price'], 0, ',', '.') }}</small>
                            @endif
                            <small class="d-block fw-semibold text-theme-primary">Subtotal Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</small>
                        </div>
                    </div>
                    <div class="text-end flex-shrink-0">
                        <div class="d-flex align-items-center justify-content-end gap-1 mb-2">
                            <button wire:click="decreaseQty('{{ $item['key'] }}')" class="qty-btn text-theme" aria-label="Kurangi jumlah {{ $item['name'] }}" @disabled($item['qty'] <= 1)>−</button>
                            <span class="qty-value" aria-live="polite">{{ $item['qty'] }}</span>
                            <button wire:click="increaseQty('{{ $item['key'] }}')" class="qty-btn text-theme" aria-label="Tambah jumlah {{ $item['name'] }}">+</button>
                        </div>
                        <button wire:click="removeItem('{{ $item['key'] }}')" class="btn btn-sm btn-outline-danger" style="min-height:36px;">Hapus</button>
                    </div>
                </div>
            </div>
    @endforeach

    {{-- Footer --}}
    <div class="footer bg-white p-3 shadow-lg mb-5">

        @error('checkout')
            <div class="alert alert-danger py-2" style="font-size:.85rem;">{{ $message }}</div>
        @enderror

        {{-- Pilih Jenis Pesanan --}}
        @if($orderMode === 'both')
        <div class="mb-3">
            <p class="text-muted mb-2" style="{{ $labelStyle }}">Jenis Pesanan</p>
            <div class="d-flex gap-2">
                <button wire:click="selectOrderType('delivery')"
                    class="btn flex-fill py-2 d-flex flex-column align-items-center gap-1 {{ $order_type === 'delivery' ? 'btn-theme' : 'btn-outline-theme' }}"
                    style="font-size:0.82rem; border-radius:12px; min-height:48px;">
                    <span class="fw-semibold">Antar ke Alamat</span>
                </button>
                <button wire:click="selectOrderType('takeaway')"
                    class="btn flex-fill py-2 d-flex flex-column align-items-center gap-1 {{ $order_type === 'takeaway' ? 'btn-theme' : 'btn-outline-theme' }}"
                    style="font-size:0.82rem; border-radius:12px; min-height:48px;">
                    <span class="fw-semibold">Ambil Sendiri</span>
                </button>
            </div>
            @error('order_type')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
        @endif

        {{-- Delivery: Alamat + Lokasi + Info Ongkir --}}
        @if($order_type === 'delivery')
        <div class="mb-3" x-data="{
                locating: false,
                locate() {
                    if (!navigator.geolocation) {
                        $wire.setLocationFailed('Browser Anda tidak mendukung fitur lokasi. Silakan pilih Ambil Sendiri.');
                        return;
                    }
                    this.locating = true;
                    navigator.geolocation.getCurrentPosition(
                        (pos) => { $wire.setCustomerLocation(pos.coords.latitude, pos.coords.longitude).then(() => this.locating = false); },
                        (err) => {
                            this.locating = false;
                            let msg = 'Lokasi tidak dapat dideteksi. Coba lagi.';
                            if (err.code === 1) msg = 'Izin lokasi ditolak. Aktifkan izin lokasi untuk situs ini di pengaturan browser, lalu coba lagi.';
                            else if (err.code === 3) msg = 'Deteksi lokasi terlalu lama. Pastikan GPS aktif, lalu coba lagi.';
                            $wire.setLocationFailed(msg);
                        },
                        { timeout: 15000, maximumAge: 60000, enableHighAccuracy: true }
                    );
                }
            }">
            <label for="alamat_pengantaran" class="text-muted mb-1 d-block" style="{{ $labelStyle }}">Alamat Pengantaran</label>
            <textarea id="alamat_pengantaran" wire:model.blur="alamat_pengantaran" rows="2" maxlength="500"
                class="form-control mb-2" style="font-size:0.9rem; border-radius:10px;"
                placeholder="Nama jalan, nomor rumah, patokan..."></textarea>
            @error('alamat_pengantaran')
                <div class="text-danger small mb-2">{{ $message }}</div>
            @enderror

            {{-- Lokasi dipakai untuk menghitung ongkir & memastikan alamat dalam jangkauan --}}
            @if($customer_lat)
                <div class="rounded-3 p-2" style="background:#f0fdf4; border:1px solid #bbf7d0; font-size:0.85rem; color:#166534;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span><i class="fas fa-map-marker-alt me-1"></i> Lokasi terdeteksi · {{ number_format($ongkir_distance_km, 1, ',', '.') }} km</span>
                        <button type="button" @click="locate()" class="btn btn-sm btn-link p-0" style="color:#166534; font-size:.8rem;">Perbarui</button>
                    </div>
                    @if($web_settings->ongkir_enabled)
                        <div class="d-flex justify-content-between">
                            <span>Ongkos kirim</span>
                            <span class="fw-bold" style="color:{{ $ongkir > 0 ? '#e65100' : '#166534' }};">
                                {{ $ongkir > 0 ? 'Rp'.number_format($ongkir, 0, ',', '.') : 'Gratis' }}
                            </span>
                        </div>
                    @endif
                </div>
            @else
                <button type="button" @click="locate()" :disabled="locating"
                        class="btn btn-outline-theme w-100 d-flex align-items-center justify-content-center gap-2"
                        style="border-radius:10px; min-height:44px; font-size:.9rem;">
                    <span x-show="!locating"><i class="fas fa-location-arrow"></i> Gunakan Lokasi Saya</span>
                    <span x-show="locating" x-cloak><span class="spinner-border spinner-border-sm me-1"></span> Mendeteksi lokasi...</span>
                </button>
                <div class="form-text" style="font-size:.78rem;">Lokasi dibutuhkan untuk menghitung ongkir dan memastikan alamat dalam jangkauan pengantaran.</div>
                @if($location_error)
                    <div class="rounded-3 p-2 mt-2" role="alert" style="background:#fff1f2; border:1px solid #fecdd3; font-size:0.85rem; color:#9f1239;">
                        <i class="fas fa-exclamation-triangle me-1"></i> {{ $location_error }}
                        @if($orderMode === 'both')
                            <button type="button" wire:click="selectOrderType('takeaway')" class="btn btn-sm btn-link p-0 d-block mt-1" style="font-size:0.85rem; color:#9f1239; text-decoration:underline;">
                                Ganti ke Ambil Sendiri
                            </button>
                        @endif
                    </div>
                @endif
                @error('lokasi')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            @endif
        </div>
        @endif

        {{-- Voucher Pelanggan --}}
        @if(isset($customer) && $customer)
        <div class="mb-3">
            <p class="text-muted mb-2" style="{{ $labelStyle }}">
                <i class="fas fa-ticket-alt me-1"></i>Voucher Saya
            </p>
            @if($applied_voucher)
                <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <div>
                        <span class="fw-bold" style="font-size:0.85rem; color:#166534;">{{ $applied_voucher->code }}</span>
                        <span class="text-muted ms-1" style="font-size:0.8rem;">— {{ $applied_voucher->formatted_discount }}</span>
                    </div>
                    <button wire:click="clearVoucher" class="btn btn-sm btn-link text-danger p-0 ms-2" style="font-size:0.85rem;">Hapus</button>
                </div>
            @elseif($customerVouchers->isNotEmpty())
                <div class="d-flex flex-column gap-1">
                    @foreach($customerVouchers as $v)
                    @php $eligible = $summary['subtotal'] >= $v->min_purchase; @endphp
                    <button wire:click="applyVoucher({{ $v->id }})"
                            class="btn btn-sm text-start d-flex align-items-center justify-content-between w-100"
                            style="border:1px {{ $eligible ? 'solid #bbf7d0;background:#f0fdf4' : 'dashed #dee2e6;background:#f9f9f9;opacity:.6' }};border-radius:8px;padding:10px 12px;"
                            {{ $eligible ? '' : 'disabled' }}>
                        <span>
                            <span class="fw-bold" style="color:#166534;font-size:0.85rem;">{{ $v->formatted_discount }}</span>
                            @if($v->min_purchase > 0)
                            <span class="text-muted" style="font-size:0.75rem;"> · Min Rp{{ number_format($v->min_purchase, 0, ',', '.') }}</span>
                            @endif
                        </span>
                        <span style="font-family:monospace;font-size:0.78rem;color:#6b7280;">{{ $v->code }}</span>
                    </button>
                    @endforeach
                </div>
                @if($voucher_error)
                    <div class="text-danger small mt-1">{{ $voucher_error }}</div>
                @endif
            @else
                <div class="text-muted" style="font-size:0.82rem;">
                    <i class="fas fa-info-circle me-1"></i>Tidak ada voucher tersedia.
                    <a href="{{ route('customer.account') }}" wire:navigate style="color:var(--button-bg-color);">Lihat voucher</a>
                </div>
            @endif
        </div>
        @endif

        {{-- Kode Diskon --}}
        <div class="mb-3">
            <label for="kode_promo" class="text-muted mb-2 d-block" style="{{ $labelStyle }}">Kode Promo</label>
            @if($applied_discount)
                <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <div>
                        <span class="fw-bold" style="font-size:0.85rem; color:#166534;">{{ $applied_discount->code }}</span>
                        <span class="text-muted ms-1" style="font-size:0.8rem;">— {{ $applied_discount->formatted_discount }}</span>
                    </div>
                    <button wire:click="clearDiscountCode" class="btn btn-sm btn-link text-danger p-0 ms-2" style="font-size:0.85rem;">Hapus</button>
                </div>
            @else
                <div class="input-group">
                    <input id="kode_promo" type="text" class="form-control" wire:model.live.debounce.400ms="discount_code"
                        placeholder="Contoh: HEMAT10" wire:keydown.enter="applyDiscountCode" autocomplete="off"
                        style="border-radius:10px 0 0 10px; font-size:.9rem;">
                    <button wire:click="applyDiscountCode" class="btn btn-theme" type="button"
                        style="border-radius:0 10px 10px 0; font-size:0.85rem; min-width:64px;">Pakai</button>
                </div>
                @if($discount_error)
                    <div class="text-danger small mt-1">{{ $discount_error }}</div>
                @endif
            @endif
        </div>

        {{-- Ringkasan Harga --}}
        <div class="py-2 border-top border-bottom mb-3">
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted" style="font-size:0.85rem;">Subtotal</span>
                <span style="font-size:0.85rem;">Rp{{ number_format($summary['subtotal'], 0, ',', '.') }}</span>
            </div>
            @if($summary['discount'] > 0)
            <div class="d-flex justify-content-between mb-1">
                <span style="font-size:0.85rem; color:#166534;">Diskon ({{ $applied_discount->code }})</span>
                <span style="font-size:0.85rem; color:#166534;">-Rp{{ number_format($summary['discount'], 0, ',', '.') }}</span>
            </div>
            @endif
            @if($summary['voucher'] > 0)
            <div class="d-flex justify-content-between mb-1">
                <span style="font-size:0.85rem; color:#166534;"><i class="fas fa-ticket-alt me-1"></i>Voucher ({{ $applied_voucher->code }})</span>
                <span style="font-size:0.85rem; color:#166534;">-Rp{{ number_format($summary['voucher'], 0, ',', '.') }}</span>
            </div>
            @endif
            @if($order_type === 'delivery' && $web_settings->ongkir_enabled)
            <div class="d-flex justify-content-between mb-1">
                <span style="font-size:0.85rem;">
                    Ongkir
                    @if($ongkir_distance_km > 0)
                        <span class="text-muted" style="font-size:0.78rem;">({{ number_format($ongkir_distance_km, 1, ',', '.') }} km)</span>
                    @endif
                </span>
                <span style="font-size:0.85rem; color:{{ $summary['ongkir'] > 0 ? '#e65100' : '#166534' }};">
                    @if($customer_lat)
                        {{ $summary['ongkir'] > 0 ? '+Rp'.number_format($summary['ongkir'], 0, ',', '.') : 'Gratis' }}
                    @else
                        <span class="text-muted" style="font-size:.78rem;">Tentukan lokasi dulu</span>
                    @endif
                </span>
            </div>
            @endif
            @if($summary['rounding'] != 0)
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted" style="font-size:0.78rem;">Pembulatan</span>
                <span class="{{ $summary['rounding'] > 0 ? 'text-success' : 'text-danger' }}" style="font-size:0.78rem;">
                    {{ $summary['rounding'] > 0 ? '+' : '-' }}Rp{{ number_format(abs($summary['rounding']), 0, ',', '.') }}
                </span>
            </div>
            @endif
            <div class="d-flex justify-content-between fw-bold mt-1">
                <span>Total</span>
                <span style="color:{{ $themeColor }};">Rp{{ number_format($summary['total'], 0, ',', '.') }}</span>
            </div>
        </div>

        <p class="text-muted text-center mb-2" style="font-size:0.78rem;">Pastikan pesanan benar. Jazakallahu khairan 🙏</p>

        <button wire:click="checkout" type="button" class="btn btn-theme w-100" style="border-radius:12px; padding:.7rem;">
            <span wire:loading.remove wire:target="checkout">Lanjut ke Konfirmasi →</span>
            <span wire:loading wire:target="checkout">
                <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
            </span>
        </button>
    </div>
    @endif

    <!-- Modal Konfirmasi Pesanan -->
    <div wire:ignore.self class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="checkoutModalLabel">Konfirmasi Pesanan</h5>
                    <small class="text-muted">
                        {{ $order_type === 'delivery' ? 'Antar ke alamat' : 'Ambil sendiri di toko' }}
                        · Total Rp{{ number_format($summary['total'], 0, ',', '.') }}
                    </small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body pt-2">
                @error('checkout')
                    <div class="alert alert-danger py-2" style="font-size:.85rem;">{{ $message }}</div>
                @enderror

                <div class="mb-3">
                    <label for="nama_pemesan" class="form-label fw-semibold" style="font-size:0.85rem;">Nama Pemesan <span class="text-danger">*</span></label>
                    <input id="nama_pemesan" type="text" class="form-control" wire:model="nama_pemesan" maxlength="100" placeholder="Nama Anda" autocomplete="name">
                    @error('nama_pemesan') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                @if($order_type === 'delivery' && $alamat_pengantaran)
                <div class="mb-3 p-2 rounded-3" style="background:#f8fafc; border:1px solid #e2e8f0; font-size:0.82rem;">
                    <div class="text-muted mb-0.5" style="font-size:0.75rem; font-weight:600;">ALAMAT PENGANTARAN</div>
                    <div>{{ $alamat_pengantaran }}</div>
                    @if($ongkir_distance_km > 0)
                    <div class="mt-1 text-muted">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ number_format($ongkir_distance_km, 1, ',', '.') }} km dari toko
                        @if($web_settings->ongkir_enabled)
                        — Ongkir: <strong style="color:{{ $summary['ongkir'] > 0 ? '#e65100' : '#166534' }}">{{ $summary['ongkir'] > 0 ? 'Rp'.number_format($summary['ongkir'], 0, ',', '.') : 'Gratis' }}</strong>
                        @endif
                    </div>
                    @endif
                </div>
                @endif

                <div class="mb-2">
                    <label for="waktu_select" class="form-label fw-semibold" style="font-size:0.85rem;">
                        Waktu {{ $order_type === 'delivery' ? 'Pengantaran' : 'Pengambilan' }} <span class="text-danger">*</span>
                    </label>
                    <div wire:ignore>
                        <select class="form-select" id="waktu_select"
                            onchange="var h=document.getElementById('waktu_input_hidden');h.value=this.value;h.dispatchEvent(new Event('input'));">
                            <option value="">-- Pilih Waktu --</option>
                        </select>
                    </div>
                    <input type="hidden" id="waktu_input_hidden" wire:model="waktu_pengantaran">
                    <div class="form-text text-muted" style="font-size:0.75rem;">
                        Minimal {{ $summary['qty'] > 5 ? '1 jam' : '30 menit' }} dari sekarang
                        @if($summary['qty'] > 5)
                            <span class="text-warning">(pesanan banyak — butuh waktu lebih)</span>
                        @endif
                    </div>
                    @error('waktu_pengantaran') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button wire:click="konfirmasiCheckout" class="btn btn-theme px-4">
                    <span wire:loading.remove wire:target="konfirmasiCheckout">Buat Pesanan</span>
                    <span wire:loading wire:target="konfirmasiCheckout">
                        <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                    </span>
                </button>
            </div>
        </div>
        </div>
    </div>

<!-- Sertakan file JavaScript untuk Local Storage -->
@push('scripts')
    <script>
        // Pastikan Livewire sudah dimuat
        document.addEventListener('livewire:init', function() {
            // Muat script cart-storage.js
            const script = document.createElement('script');
            script.src = '{{ asset('js/cart-storage.js') }}';
            script.onload = function() {
                // Sesi habis tapi keranjang masih ada di Local Storage: pulihkan ke session lalu muat ulang
                const sessionCartEmpty = @json(empty($cart));
                if (sessionCartEmpty && CartStorage.getCartFromLocal().length > 0) {
                    CartStorage.syncCartToSession().then(() => window.location.reload()).catch(() => {});
                }

                // Dengarkan event untuk memperbarui Local Storage
                Livewire.on('updateLocalCart', function(payload) {
                    CartStorage.saveCartToLocal(payload.cart ?? payload);
                });

                Livewire.on('clearLocalCart', function() {
                    CartStorage.clearLocalCart();
                });
            };
            document.head.appendChild(script);

        });
    </script>
@endpush

<script>
    function generateTimeSlots() {
        const select = document.getElementById('waktu_select');
        if (!select) return;

        const closingTime = '{{ $web_settings->closing_time ? $web_settings->closing_time->format("H:i") : "22:00" }}';
        const [closeHour, closeMin] = closingTime.split(':').map(Number);

        const totalQty = {{ $summary['qty'] }};
        const isLargeOrder = totalQty > 5;

        const now = new Date();

        // Bulatkan waktu sekarang ke atas ke kelipatan 10 menit terdekat
        const nowTotalMin = now.getHours() * 60 + now.getMinutes();
        const roundedMin = Math.ceil(nowTotalMin / 10) * 10;

        // Slot pertama: +60 menit jika pesanan > 5 item, +30 menit jika tidak
        const firstSlotMin = roundedMin + (isLargeOrder ? 60 : 30);

        const firstSlot = new Date(now);
        firstSlot.setHours(Math.floor(firstSlotMin / 60), firstSlotMin % 60, 0, 0);

        const end = new Date(now);
        end.setHours(closeHour - 1, closeMin, 0, 0);
        // Jam tutup lewat tengah malam (mis. 02:00): batas akhir ada di hari berikutnya
        if (end <= now && closeHour < 12) end.setDate(end.getDate() + 1);

        select.innerHTML = '<option value="">-- Pilih Waktu --</option>';
        const cur = new Date(firstSlot);
        while (cur <= end) {
            const hh = String(cur.getHours()).padStart(2, '0');
            const mm = String(cur.getMinutes()).padStart(2, '0');
            const label = `${hh}.${mm} WIB`;
            select.appendChild(new Option(label, label));
            cur.setMinutes(cur.getMinutes() + 30);
        }
        if (select.options.length === 1) {
            const o = new Option('Tidak ada jadwal tersedia hari ini', '');
            o.disabled = true;
            select.appendChild(o);
        }
    }

    Livewire.on('show-checkout-modal', function () {
        const el = document.getElementById('checkoutModal');
        if (!el) return;
        generateTimeSlots();
        el.addEventListener('hidden.bs.modal', function () {
            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = '';
        }, { once: true });
        bootstrap.Modal.getOrCreateInstance(el).show();
    });
</script>

</div>
