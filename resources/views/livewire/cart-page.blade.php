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
    <div class="header-button d-flex justify-content-between align-items-center pb-3">
        <a wire:navigate href="{{ route('home') }}" class="btn btn-light rounded-circle shadow-sm">
            <i class="fas fa-chevron-left"></i>
        </a>
        <h5 class="fw-bold pt-3 pe-2" style="font-size: 1.1rem;">Keranjang</h5>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 small" style="font-size: 0.85rem;">Jumlah Keranjang: {{ count($cart) }}</h5>
        <button wire:click="clearCart" class="btn btn-link text-danger text-decoration-none btn-sm">Kosongkan Keranjang</button>
    </div>
    {{-- Cart Items --}}
    @forelse($cartItems as $item)
            <div class="card mb-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        @if($item['type']==="makanan")
                        <img src="{{ $item['model']?->getFirstMediaUrl('gambar') ?? ($item['type'] === 'minuman' ? asset('default-drink.jpg') : asset('default-food.jpg')) }}"
                             class="rounded me-3"
                             alt="{{ $item['name'] }}"
                             width="60" height="60"
                             style="object-fit: cover; object-position: center;">
                        @else
                        <img src="{{ $item['model']?->getFirstMediaUrl('foto') ?? ($item['type'] === 'minuman' ? asset('default-drink.jpg') : asset('default-food.jpg')) }}"
                             class="rounded me-3"
                             alt="{{ $item['name'] }}"
                             width="60" height="60"
                             style="object-fit: cover; object-position: center;">
                        @endif
                        <div class="text-muted" style="font-size: 9pt">
                            <h6 class="mb-1 fw-bold text-theme" style="font-size: 0.9rem;">{{ $item['name'] }}</h6>
                            <small>
                                @if($item['type'] === 'minuman')
                                    @if($item['size'])Size: {{ $item['size'] }} |@endif
                                    @if($item['sugar'])Gula: {{ $item['sugar'] }} |@endif
                                @endif
                                @if($item['topping'] !== '-')Topping: {{ $item['topping'] }}@endif
                            </small><br>
                            @if($item['has_discount'])
                                <div class="d-flex align-items-center gap-1">
                                    <small class="text-decoration-line-through text-muted">Rp{{ number_format($item['regular_price'], 0, ',', '.') }}</small>
                                    <small class="text-danger fw-bold">Rp{{ number_format($item['price'], 0, ',', '.') }}</small>
                                    <small class="badge bg-danger text-white" style="font-size: 0.65rem;">{{ $item['discount_info']['discount_text'] }}</small>
                                </div>
                                <small class="text-muted">{{ $item['qty'] }}x = Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</small>
                            @else
                                <small class="text-muted">Rp{{ number_format($item['price'], 0, ',', '.') }} x {{ $item['qty'] }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="btn-group btn-group-sm mb-2">
                            <button wire:click="decreaseQty('{{ $item['key'] }}')" class="btn btn-link text-decoration-none text-theme">−</button>
                            <button class="btn btn-light" disabled>{{ $item['qty'] }}</button>
                            <button wire:click="increaseQty('{{ $item['key'] }}')" class="btn btn-link text-decoration-none text-theme">+</button>
                        </div>
                        <br>
                        <button wire:click="removeItem('{{ $item['key'] }}')" class="btn btn-sm btn-outline-danger">Hapus</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">Keranjang kosong.</div>
        @endforelse
    
    {{-- Footer --}}
    <div class="footer bg-white p-3 shadow-lg mb-5">

        {{-- Pilih Jenis Pesanan --}}
        @if($orderMode === 'both')
        <div class="mb-3">
            <p class="text-muted mb-2" style="font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em;">Jenis Pesanan</p>
            <div class="d-flex gap-2">
                <button wire:click="selectOrderType('delivery')"
                    class="btn flex-fill py-2 d-flex flex-column align-items-center gap-1 {{ $order_type === 'delivery' ? 'btn-theme' : 'btn-outline-theme' }}"
                    style="font-size:0.82rem; border-radius:12px;">
                    <i class="bi bi-truck" style="font-size:1.2rem;"></i>
                    <span class="fw-semibold">Antar ke Alamat</span>
                </button>
                <button wire:click="selectOrderType('takeaway')"
                    class="btn flex-fill py-2 d-flex flex-column align-items-center gap-1 {{ $order_type === 'takeaway' ? 'btn-theme' : 'btn-outline-theme' }}"
                    style="font-size:0.82rem; border-radius:12px;">
                    <i class="bi bi-bag" style="font-size:1.2rem;"></i>
                    <span class="fw-semibold">Ambil Sendiri</span>
                </button>
            </div>
            @error('order_type')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
        @endif

        {{-- Delivery: Alamat + Info Ongkir --}}
        @if($order_type === 'delivery')
        @php
            $locationSettings = json_encode([
                'lat'    => (float) $web_settings->latitude,
                'lng'    => (float) $web_settings->longitude,
                'radius' => (int) $web_settings->delivery_radius,
            ]);
        @endphp
        <div class="mb-3" x-data x-init="
            if (!navigator.geolocation) {
                $wire.call('setLocationFailed', 'Browser tidak mendukung fitur lokasi.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    var lat = pos.coords.latitude;
                    var lng = pos.coords.longitude;
                    var s = {{ $locationSettings }};
                    var dist = haversineMeters(lat, lng, s.lat, s.lng);
                    if (dist <= s.radius) {
                        $wire.call('setCustomerLocation', lat, lng);
                    } else {
                        $wire.call('selectOrderType', 'takeaway');
                        alert('Maaf, pengiriman hanya tersedia dalam radius ' + s.radius + ' meter dari toko.');
                    }
                },
                function(err) {
                    var msg = 'Lokasi tidak dapat dideteksi.';
                    if (err.code === 1) msg = 'Izin lokasi ditolak — aktifkan lokasi di browser.';
                    else if (err.code === 3) msg = 'Deteksi lokasi timeout — coba lagi.';
                    $wire.call('setLocationFailed', msg);
                },
                { timeout: 15000, maximumAge: 60000, enableHighAccuracy: false }
            );
        ">
            <label class="text-muted mb-1 d-block" style="font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em;">Alamat Pengantaran</label>
            <textarea wire:model.blur="alamat_pengantaran" rows="2"
                class="form-control mb-2" style="font-size:0.85rem; border-radius:10px;"
                placeholder="Tulis alamat lengkap..."></textarea>
            @error('alamat_pengantaran')
                <div class="text-danger small mb-1">{{ $message }}</div>
            @enderror

            {{-- Info Jarak & Ongkir --}}
            @if($ongkir_distance_km > 0)
                <div class="rounded-3 p-2" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="d-flex align-items-center gap-1" style="font-size:0.82rem; color:#166534;">
                            <i class="bi bi-geo-alt-fill"></i> Jarak ke toko
                        </span>
                        <span class="fw-semibold" style="font-size:0.82rem; color:#166534;">
                            {{ number_format($ongkir_distance_km, 2) }} km
                        </span>
                    </div>
                    @if(isset($web_settings) && $web_settings->ongkir_enabled)
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="d-flex align-items-center gap-1" style="font-size:0.82rem; color:#166534;">
                            <i class="bi bi-truck"></i> Ongkos kirim
                        </span>
                        <span class="fw-bold" style="font-size:0.82rem; color:{{ $ongkir > 0 ? '#e65100' : '#166534' }};">
                            {{ $ongkir > 0 ? 'Rp'.number_format($ongkir, 0, ',', '.') : 'Gratis' }}
                        </span>
                    </div>
                    @endif
                </div>
            @elseif($customer_lat)
                <div class="rounded-3 p-2" style="background:#f0fdf4; border:1px solid #bbf7d0; font-size:0.82rem; color:#166534;">
                    <i class="bi bi-check-circle-fill me-1"></i> Lokasi terdeteksi — dalam jangkauan pengiriman
                </div>
            @elseif($location_error)
                <div class="rounded-3 p-2" style="background:#fff1f2; border:1px solid #fecdd3; font-size:0.82rem; color:#9f1239;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $location_error }}
                    <button wire:click="selectOrderType('delivery')" class="btn btn-sm btn-link p-0 ms-2" style="font-size:0.8rem; color:#9f1239; text-decoration:underline;">
                        Coba lagi
                    </button>
                </div>
            @else
                <div class="rounded-3 p-2 text-center" style="background:#fff7ed; border:1px solid #fed7aa; font-size:0.82rem; color:#9a3412;">
                    <span class="spinner-border spinner-border-sm me-1" style="width:.75rem;height:.75rem;"></span>
                    Mendeteksi lokasi...
                </div>
            @endif
        </div>
        @endif

        {{-- Kode Diskon --}}
        <div class="mb-3">
            <p class="text-muted mb-2" style="font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em;">Kode Diskon</p>
            @if($applied_discount)
                <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <div>
                        <span class="fw-bold" style="font-size:0.85rem; color:#166534;">{{ $applied_discount->code }}</span>
                        <span class="text-muted ms-1" style="font-size:0.8rem;">— {{ $applied_discount->formatted_discount }}</span>
                    </div>
                    <button wire:click="clearDiscountCode" class="btn btn-sm btn-link text-danger p-0 ms-2" style="font-size:0.8rem;">Hapus</button>
                </div>
            @else
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" wire:model.live="discount_code"
                        placeholder="Masukkan kode diskon" wire:keydown.enter="applyDiscountCode"
                        style="border-radius:10px 0 0 10px;">
                    <button wire:click="applyDiscountCode" class="btn btn-theme" type="button"
                        style="border-radius:0 10px 10px 0; font-size:0.82rem;">Pakai</button>
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
                <span style="font-size:0.85rem;">Rp{{ number_format($originalTotal, 0, ',', '.') }}</span>
            </div>
            @if($discountAmount > 0)
            <div class="d-flex justify-content-between mb-1">
                <span style="font-size:0.85rem; color:#166534;">Diskon ({{ $applied_discount->code }})</span>
                <span style="font-size:0.85rem; color:#166534;">-Rp{{ number_format($discountAmount, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($order_type === 'delivery' && isset($web_settings) && $web_settings->ongkir_enabled)
            <div class="d-flex justify-content-between mb-1">
                <span style="font-size:0.85rem;">
                    Ongkir
                    @if($ongkir_distance_km > 0)
                        <span class="text-muted" style="font-size:0.78rem;">({{ number_format($ongkir_distance_km, 1) }} km)</span>
                    @endif
                </span>
                <span style="font-size:0.85rem; color:{{ $ongkirAmount > 0 ? '#e65100' : '#166534' }};">
                    @if($ongkir_distance_km > 0)
                        {{ $ongkirAmount > 0 ? '+Rp'.number_format($ongkirAmount, 0, ',', '.') : 'Gratis' }}
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </span>
            </div>
            @endif
            @if($roundingAmount != 0)
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted" style="font-size:0.78rem;">Pembulatan</span>
                <span class="{{ $roundingAmount > 0 ? 'text-success' : 'text-danger' }}" style="font-size:0.78rem;">
                    {{ $roundingAmount > 0 ? '+' : '' }}Rp{{ number_format(abs($roundingAmount), 0, ',', '.') }}
                </span>
            </div>
            @endif
            <div class="d-flex justify-content-between fw-bold mt-1">
                <span>Total</span>
                <span style="color:{{ $themeColor }};">Rp{{ number_format($grandTotal, 0, ',', '.') }}</span>
            </div>
        </div>

        <p class="text-muted text-center mb-2" style="font-size:0.75rem;">Pastikan pesanan benar. Jazakallahu khairan 🙏</p>

        <button wire:click="checkout" type="button" class="btn btn-theme w-100" style="border-radius:12px; padding:.65rem;">
            <span wire:loading.remove wire:target="checkout">Lanjut ke Konfirmasi →</span>
            <span wire:loading wire:target="checkout">
                <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
            </span>
        </button>
    </div>

    <!-- Modal Konfirmasi Pesanan -->
    <div wire:ignore.self class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="checkoutModalLabel">Konfirmasi Pesanan</h5>
                    <small class="text-muted">
                        {{ $order_type === 'delivery' ? 'Antar ke alamat' : 'Ambil sendiri di toko' }}
                    </small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body pt-2">
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Nama Pemesan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" wire:model.defer="nama_pemesan" placeholder="Nama Anda">
                    @error('nama_pemesan') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                @if($order_type === 'delivery' && $alamat_pengantaran)
                <div class="mb-3 p-2 rounded-3" style="background:#f8fafc; border:1px solid #e2e8f0; font-size:0.82rem;">
                    <div class="text-muted mb-0.5" style="font-size:0.75rem; font-weight:600;">ALAMAT PENGANTARAN</div>
                    <div>{{ $alamat_pengantaran }}</div>
                    @if($ongkir_distance_km > 0)
                    <div class="mt-1 text-muted">
                        <i class="bi bi-geo-alt-fill me-1"></i>{{ number_format($ongkir_distance_km, 1) }} km dari toko
                        @if(isset($web_settings) && $web_settings->ongkir_enabled)
                        — Ongkir: <strong style="color:{{ $ongkirAmount > 0 ? '#e65100' : '#166534' }}">{{ $ongkirAmount > 0 ? 'Rp'.number_format($ongkirAmount, 0, ',', '.') : 'Gratis' }}</strong>
                        @endif
                    </div>
                    @endif
                </div>
                @endif

                <div class="mb-2">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">
                        Waktu {{ $order_type === 'delivery' ? 'Pengantaran' : 'Pengambilan' }} <span class="text-danger">*</span>
                    </label>
                    <div wire:ignore>
                        <select class="form-select" id="waktu_select"
                            onchange="var h=document.getElementById('waktu_input_hidden');h.value=this.value;h.dispatchEvent(new Event('input'));">
                            <option value="">-- Pilih Waktu --</option>
                        </select>
                    </div>
                    <input type="hidden" id="waktu_input_hidden" wire:model.defer="waktu_pengantaran">
                    <div class="form-text text-muted" style="font-size:0.75rem;">Minimal 30 menit dari sekarang</div>
                    @error('waktu_pengantaran') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button wire:click="konfirmasiCheckout" class="btn btn-theme px-4">
                    <span wire:loading.remove wire:target="konfirmasiCheckout">Kirim ke WhatsApp</span>
                    <span wire:loading wire:target="konfirmasiCheckout">
                        <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                    </span>
                </button>
            </div>
        </div>
        </div>
    </div>
  
<x-mobile-nav/>

<!-- Sertakan file JavaScript untuk Local Storage -->
@push('scripts')
    <script>
        // Pastikan Livewire sudah dimuat
        document.addEventListener('livewire:init', function() {
            // Muat script cart-storage.js
            const script = document.createElement('script');
            script.src = '{{ asset('js/cart-storage.js') }}';
            script.onload = function() {
                // Inisialisasi keranjang dari Local Storage saat halaman dimuat
                document.addEventListener('livewire:initialized', function() {
                    const localCart = CartStorage.getCartFromLocal();
                    if (localCart.length > 0) {
                        // Sinkronkan ke session jika ada data di Local Storage
                        CartStorage.syncCartToSession()
                            .then(() => {
                                // Refresh komponen Livewire setelah sinkronisasi
                                @this.call('$refresh');
                            });
                    }
                });

                // Dengarkan event untuk memperbarui Local Storage
                Livewire.on('updateLocalCart', function(cart) {
                    CartStorage.saveCartToLocal(cart);
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
    window.locationSettings = @json(\App\Models\WebSetting::first(['latitude', 'longitude', 'delivery_radius']));

    function haversineMeters(lat1, lon1, lat2, lon2) {
        const R = 6371e3;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2)**2 +
                  Math.cos(lat1*Math.PI/180) * Math.cos(lat2*Math.PI/180) * Math.sin(dLon/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    function generateTimeSlots() {
        const select = document.getElementById('waktu_select');
        if (!select) return;

        const closingTime = '{{ $web_settings->closing_time ? $web_settings->closing_time->format("H:i") : "22:00" }}';
        const [closeHour, closeMin] = closingTime.split(':').map(Number);

        const now = new Date();
        const min = new Date(now.getTime() + 30 * 60 * 1000);
        const m = min.getMinutes();
        if (m > 0 && m <= 30) min.setMinutes(30, 0, 0);
        else if (m > 30) min.setHours(min.getHours() + 1, 0, 0, 0);
        else min.setSeconds(0, 0);

        const end = new Date(now);
        end.setHours(closeHour - 1, closeMin, 0, 0);

        select.innerHTML = '<option value="">-- Pilih Waktu --</option>';
        const cur = new Date(min);
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
