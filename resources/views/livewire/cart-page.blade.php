<div class="container p-4 bg-white mb-5" style="height: 100vh; display: flex; flex-direction: column;">
    <style>
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
        <h5 class="fw-bold pt-3 pe-2">Keranjang</h5>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 small">Jumlah Keranjang: {{ count($cart) }}</h5>
        <button wire:click="clearCart" class="btn btn-link text-danger text-decoration-none btn-sm">Kosongkan Keranjang</button>
    </div>

    {{-- Scrollable Cart --}}
    <div  style="padding-bottom:200px;">
        @forelse($cartItems as $item)
            <div class="card mb-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ $item['minuman_model']?->getFirstMediaUrl('foto') ?? asset('default-drink.jpg') }}"
                             class="rounded me-3"
                             alt="{{ $item['minuman'] }}"
                             width="60" height="60"
                             style="object-fit: cover; object-position: center;">
                        <div class="text-muted" style="font-size: 10pt">
                            <h6 class="mb-1 fw-bold text-success">{{ $item['minuman'] }}</h6>
                            <small>Size: {{ $item['size'] }} | Gula: {{ $item['sugar'] }} | Topping: {{ $item['topping'] }}</small><br>
                            <small class="text-muted">{{ number_format($item['price'], 0, ',', '.') }} x {{ $item['qty'] }}</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="btn-group btn-group-sm mb-2">
                            <button wire:click="decreaseQty('{{ $item['key'] }}')" class="btn btn-link text-decoration-none text-success">−</button>
                            <button class="btn btn-light" disabled>{{ $item['qty'] }}</button>
                            <button wire:click="increaseQty('{{ $item['key'] }}')" class="btn btn-link text-decoration-none text-success">+</button>
                        </div>
                        <br>
                        <button wire:click="removeItem('{{ $item['key'] }}')" class="btn btn-sm btn-outline-danger">Hapus</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">Keranjang kosong.</div>
        @endforelse
    </div>
    
    {{-- Footer (tetap di bawah) --}}
    <div class="footer fixed-bottom bg-white p-3 shadow-lg" style="margin-bottom: 2.5rem">
        <div class="text-center mb-2">
            <h5 class="fw-bold mb-1">Total: {{ number_format($total, 0, ',', '.') }} IDR</h5>
            <p class="text-muted small mb-2">Pastikan pesanan anda benar, siapkan uang pas jika memungkinkan, Jazakallahu khairan</p>
        </div>
        <button id="checkoutBtn" class="btn btn-success w-100 mb-2">
            Checkout
        </button>
        
    </div>

    <!-- Modal Konfirmasi Pesanan -->
    <div wire:ignore.self class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title fw-bold" id="checkoutModalLabel">Konfirmasi Pesanan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
            <div class="mb-2">
                <label class="form-label">Nama Pemesan</label>
                <input type="text" class="form-control" wire:model.defer="nama_pemesan">
            </div>
    
            <div class="mb-2">
                <label class="form-label">Alamat Pengantaran</label>
                <textarea class="form-control" wire:model.defer="alamat_pengantaran"></textarea>
            </div>
    
            <div class="mb-3">
                <label class="form-label">Waktu Pengantaran</label>
                <input type="text" class="form-control" wire:model.defer="waktu_pengantaran" placeholder="Misal: 16.00 WIB / Sekarang">
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button wire:click="konfirmasiCheckout" class="btn btn-success">
                Kirim ke WhatsApp
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
    // Get or initialize location settings
    if (typeof window.locationSettings === 'undefined') {
        window.locationSettings = @json(\App\Models\WebSetting::first(['latitude', 'longitude', 'delivery_radius']));
    }

    document.getElementById('checkoutBtn')?.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent default form submission

        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung fitur lokasi.');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                console.log(userLat, userLng);
                // Use settings from database
                const centerLat = parseFloat(window.locationSettings.latitude);
                const centerLng = parseFloat(window.locationSettings.longitude);
                const maxRadius = parseInt(window.locationSettings.delivery_radius);

                const distance = getDistanceFromLatLonInMeters(userLat, userLng, centerLat, centerLng);
                console.log(distance);
                if (distance <= maxRadius) {
                    // If within radius, show checkout modal
                    const modal = new bootstrap.Modal(document.getElementById('checkoutModal'));
                    modal.show();
                } else {
                    alert(`Layanan ini hanya tersedia dalam radius ${maxRadius} meter dari lokasi toko.`);
                }
            },
            function (error) {
                if (error.code === error.PERMISSION_DENIED) {
                    alert('Akses lokasi ditolak. Silakan izinkan lokasi di pengaturan browser.');
                } else {
                    alert('Tidak bisa mendeteksi lokasi. Pastikan GPS aktif dan coba lagi.');
                }
            }
        );
    });

    // Fungsi Haversine untuk hitung jarak antar koordinat
    function getDistanceFromLatLonInMeters(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // Radius bumi dalam meter
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;

        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) *
            Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        const distance = R * c;
        return distance;
    }
</script>

<script>
    window.addEventListener('close-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('checkoutModal'));
        if (modal) modal.hide();
    });
</script>

</div>
