<div class="container p-4 bg-white mb-5" style="height: 100vh; display: flex; flex-direction: column;" wire:poll.60s>
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
                            <h6 class="mb-1 fw-bold text-theme">{{ $item['minuman'] }}</h6>
                            <small>Size: {{ $item['size'] }} | Gula: {{ $item['sugar'] }} | Topping: {{ $item['topping'] }}</small><br>
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
    </div>
    
    {{-- Footer (tetap di bawah) --}}
    <div class="footer fixed-bottom bg-white p-3 shadow-lg" style="margin-bottom: 2.5rem">
        <div class="text-center mb-2">
            <h5 class="fw-bold mb-1">Total: Rp{{ number_format($total, 0, ',', '.') }}</h5>
            <p class="text-muted small mb-2">Pastikan pesanan anda benar, siapkan uang pas jika memungkinkan, Jazakallahu khairan</p>
        </div>
        <button id="checkoutBtn" type="button" class="btn btn-theme w-100 mb-2">
            Checkout
        </button>
        
    </div>

    <!-- Modal Pilih Jenis Pesanan -->
    <div wire:ignore.self class="modal fade" id="orderTypeModal" tabindex="-1" aria-labelledby="orderTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="orderTypeModalLabel">Pilih Jenis Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-3">
                        <button id="delivery-btn" class="btn btn-lg btn-outline-theme d-flex flex-column align-items-center py-3">
                            <i class="bi bi-truck fs-1 mb-2"></i>
                            <span class="fw-bold">Antar ke Alamat</span>
                            <small class="text-muted">Pesanan diantar ke lokasi Anda</small>
                        </button>
                        
                        <button id="takeaway-btn" class="btn btn-lg btn-outline-theme d-flex flex-column align-items-center py-3">
                            <i class="bi bi-bag fs-1 mb-2"></i>
                            <span class="fw-bold">Ambil Sendiri</span>
                            <small class="text-muted">Anda mengambil pesanan di toko</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>
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
    
            @if($order_type === 'delivery')
            <div class="mb-2">
                <label class="form-label">Alamat Pengantaran</label>
                <textarea class="form-control" wire:model.defer="alamat_pengantaran"></textarea>
            </div>
            @endif
    
            <div class="mb-3">
                <label class="form-label">Waktu {{ $order_type === 'delivery' ? 'Pengantaran' : 'Pengambilan' }}</label>
                <input type="text" class="form-control" wire:model.defer="waktu_pengantaran" placeholder="Misal: 16.00 WIB / Sekarang">
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button wire:click="konfirmasiCheckout" class="btn btn-theme">
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
    // Global location settings
    window.locationSettings = @json(\App\Models\WebSetting::first(['latitude', 'longitude', 'delivery_radius']));
    
    // Track initialization state to prevent duplicate handlers
    window.checkoutInitialized = false;

    // Haversine function to calculate distance
    function getDistanceFromLatLonInMeters(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // Radius of earth in meters
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;

        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) *
            Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }
    
    // Check delivery location function
    function checkDeliveryLocation(successCallback) {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung fitur lokasi.');
            return;
        }
        
        // Show loading indicator
        const checkoutBtn = document.getElementById('checkoutBtn');
        if (checkoutBtn) {
            checkoutBtn.innerHTML = 'Memeriksa lokasi... <span class="spinner-border spinner-border-sm"></span>';
        }
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // Reset button text
                if (checkoutBtn) checkoutBtn.textContent = 'Checkout';
                
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                
                const centerLat = parseFloat(window.locationSettings.latitude);
                const centerLng = parseFloat(window.locationSettings.longitude);
                const maxRadius = parseInt(window.locationSettings.delivery_radius);
                
                const distance = getDistanceFromLatLonInMeters(userLat, userLng, centerLat, centerLng);
                
                if (distance <= maxRadius) {
                    // Within delivery radius
                    successCallback();
                } else {
                    alert(`Layanan ini hanya tersedia dalam radius ${maxRadius} meter dari lokasi toko.`);
                }
            },
            function(error) {
                // Reset button text
                if (checkoutBtn) checkoutBtn.textContent = 'Checkout';
                
                if (error.code === error.PERMISSION_DENIED) {
                    alert('Akses lokasi ditolak. Silakan izinkan lokasi di pengaturan browser.');
                } else {
                    alert('Tidak bisa mendeteksi lokasi. Pastikan GPS aktif dan coba lagi.');
                }
            }
        );
    }
    
    // The main function to attach all event handlers
    function initCartPage() {
        // Skip if already initialized to prevent duplicate handlers
        if (window.checkoutInitialized) return;
        
        try {
            // Get required elements
            const checkoutBtn = document.getElementById('checkoutBtn');
            const deliveryBtn = document.getElementById('delivery-btn');
            const takeawayBtn = document.getElementById('takeaway-btn');
            const orderTypeModalEl = document.getElementById('orderTypeModal');
            const checkoutModalEl = document.getElementById('checkoutModal');
            
            // Check if elements exist
            if (!checkoutBtn || !orderTypeModalEl || !checkoutModalEl) return;
            
            // Initialize Bootstrap modals
            const orderTypeModal = new bootstrap.Modal(orderTypeModalEl);
            const checkoutModal = new bootstrap.Modal(checkoutModalEl);
            
            // Clean up modal artifacts when hidden
            function cleanupModal() {
                // Remove any lingering modal backdrops
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                // Remove modal-open class from body
                document.body.classList.remove('modal-open');
                // Reset body padding if it was adjusted
                document.body.style.paddingRight = '';
            }
            
            // Add cleanup on hidden for modal
            orderTypeModalEl.addEventListener('hidden.bs.modal', function () {
                // Only clean up if no other modals are shown
                if (document.querySelectorAll('.modal.show').length === 0) {
                    setTimeout(() => {
                        cleanupModal();
                    }, 150);
                }
            });
            
            checkoutModalEl.addEventListener('hidden.bs.modal', function () {
                setTimeout(() => {
                    @this.set('order_type', null);
                    cleanupModal();
                }, 150);
            });
            
            // Add checkout button click handler
            checkoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const orderMode = '{{ $orderMode }}';
                
                if (orderMode === 'both') {
                    orderTypeModal.show();
                } else if (orderMode === 'delivery') {
                    // Check location for delivery
                    checkDeliveryLocation(function() {
                        checkoutModal.show();
                    });
                } else {
                    // For takeaway, show checkout modal directly
                    checkoutModal.show();
                }
            });
            
            // Add click handlers to order type buttons
            if (deliveryBtn) {
                deliveryBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    orderTypeModal.hide();
                    @this.set('order_type', 'delivery');
                    
                    // For delivery, check location first
                    checkDeliveryLocation(function() {
                        checkoutModal.show();
                    });
                });
            }
            
            if (takeawayBtn) {
                takeawayBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    orderTypeModal.hide();
                    @this.set('order_type', 'takeaway');
                    checkoutModal.show();
                });
            }
            
            // Mark as initialized
            window.checkoutInitialized = true;
        } catch (error) {
            // Silent error handling
        }
    }
    
    // Initialize on different events to ensure it works both on page load and navigation
    document.addEventListener('DOMContentLoaded', initCartPage);
    document.addEventListener('livewire:load', initCartPage);
    
    // Initialize when Livewire navigates to this page
    document.addEventListener('livewire:navigated', function() {
        window.checkoutInitialized = false; // Reset to allow re-initialization
        initCartPage();
    });
</script>

</div>
