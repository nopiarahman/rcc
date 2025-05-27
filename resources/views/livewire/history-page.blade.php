<div class="container p-4 bg-white mb-5" style="min-height: 100vh; display: flex; flex-direction: column;">
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
        .card {
            animation: fadeSlideUp 0.6s ease-out both;
            font-size: 0.9rem;
        }
        .modal {
            font-size: 0.9rem;
        }
        .modal-title {
            font-size: 1.1rem;
        }
        .table {
            font-size: 0.85rem;
        }
        .badge {
            font-size: 0.75rem;
            font-weight: normal;
        }
</style>
    {{-- Header mirip cart-page --}}
        <div class="header-button d-flex justify-content-between align-items-center pb-3">
            <a wire:navigate href="{{ route('home') }}" class="btn btn-light rounded-circle shadow-sm">
                <i class="fas fa-chevron-left"></i>
            </a>
            <h5 class="fw-bold pt-3 pe-2 mb-0 text-theme" style="font-size: 1.1rem;">Riwayat Pesanan</h5>
        </div>
        <p class="small text-gray-400 mb-4" style="text-align: right;">Klik pesanan untuk melihat lebih detail</p>

        @if($orders->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-history fa-3x text-muted"></i>
                </div>
                <h5 class="mb-2">Belum ada riwayat pesanan</h5>
                <p class="text-gray-400 mb-4">Pesanan yang Anda buat akan muncul di sini</p>
            </div>
        @else
            <div>
                @foreach($orders as $order)
                    <div class="card mb-2 rounded-3 btn btn-light" style="text-align: left;" wire:click="viewOrder({{ $order->id }})">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-bold text-theme">
                                    <span class="text-muted" style="font-size: 0.8rem;">{{ $order->created_at->format('d M Y H:i') }}</span>
                                </div>
                                @php
                                    $statusColors = [
                                        'menunggu_konfirmasi' => 'bg-warning text-dark',
                                        'diproses' => 'bg-info text-dark',
                                        'diantar' => 'bg-primary text-white',
                                        'dikirim' => 'bg-secondary text-white',
                                        'selesai' => 'bg-success text-white',
                                        'dibatalkan' => 'bg-danger text-white',
                                    ];
                                    $statusText = [
                                        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                                        'diproses' => 'Diproses',
                                        'diantar' => 'Diantar',
                                        'dikirim' => 'Dikirim',
                                        'selesai' => 'Selesai',
                                        'dibatalkan' => 'Dibatalkan',
                                    ];
                                @endphp
                                <span class="badge {{ $statusColors[$order->status] ?? 'bg-light text-dark' }}">
                                    {{ $statusText[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </div>
                            <div class="mb-1">
                                <span class="fw-semibold" style="font-size: 0.9rem;">{{ $order->details->count() }} Pesanan</span>
                            </div>
                            <div class="mb-1">
                                <span class="fw-semibold" style="font-size: 0.9rem;">Total: Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    <!-- Order Detail Modal -->
    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-theme">Detail Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($selectedOrder)
                        <div class="mb-3">
                            <p class="mb-1"><strong>Nomor Pesanan:</strong> {{ $selectedOrder->nomor_pesanan }}</p>
                            <p class="mb-1"><strong>Tanggal:</strong> {{ $selectedOrder->created_at->format('d M Y H:i') }}</p>
                            <p class="mb-1"><strong>Nama Pemesan:</strong> {{ $selectedOrder->nama_pemesan }}</p>
                            <p class="mb-1"><strong>Alamat:</strong> {{ $selectedOrder->alamat_pengantaran }}</p>
                            <p class="mb-3"><strong>Waktu Pengantaran:</strong> {{ $selectedOrder->waktu_pengantaran }}</p>
                            
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-end">Harga</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($selectedOrder->details as $item)
                                            <tr>
                                                <td>
                                                    {{ $item->nama_minuman }}
                                                    @if($item->size || $item->gula || $item->topping)
                                                        <div class="text-muted small">
                                                            {{ $item->size ?? '' }}
                                                            {{ $item->gula ? '• Gula: ' . $item->gula : '' }}
                                                            {{ $item->topping ? '• Topping: ' . $item->topping : '' }}
                                                        </div>
                                                    @endif
                                                    @if($item->catatan)
                                                        <div class="text-muted small">Note: {{ $item->catatan }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-end">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                                <td class="text-center">{{ $item->qty }}</td>
                                                <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <th colspan="3" class="text-end">Total</th>
                                            <th class="text-end">Rp {{ number_format($selectedOrder->total_harga, 0, ',', '.') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        @if($selectedOrder)
                            @if($selectedOrder->status === 'menunggu_konfirmasi')
                                <button type="button" class="btn btn-outline-danger" 
                                        wire:click="cancelOrder({{ $selectedOrder->id }})"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove>Batalkan Pesanan</span>
                                    <span wire:loading>Membatalkan...</span>
                                </button>
                            @elseif($selectedOrder->status !== 'dibatalkan' && $selectedOrder->status !== 'selesai')
                                <div class="text-muted small">
                                    <i class="fas fa-info-circle"></i> Untuk membatalkan pesanan, harap hubungi admin
                                </div>
                            @endif
                        @endif
                    </div>
                    <button type="button" class="btn btn-outline-theme" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Track initialization state
        window.historyPageInitialized = false;
        
        // Function to initialize all event handlers and functionality
        function initHistoryPage() {
            // Skip if already initialized
            if (window.historyPageInitialized) return;
            
            try {
                // Initialize modal variable
                let orderModal = null;
                
                // Function to show modal
                const showOrderModal = () => {
                    if (!orderModal) {
                        const modalEl = document.getElementById('orderDetailModal');
                        if (!modalEl) return;
                        
                        orderModal = new bootstrap.Modal(modalEl);
                        
                        // Clean up modal instance when hidden
                        modalEl.addEventListener('hidden.bs.modal', function () {
                            setTimeout(() => {
                                @this.set('selectedOrder', null, false);
                                // Remove any lingering modal backdrop
                                const backdrops = document.querySelectorAll('.modal-backdrop');
                                backdrops.forEach(backdrop => backdrop.remove());
                                // Remove modal-open class from body
                                document.body.classList.remove('modal-open');
                                // Reset body padding if it was adjusted
                                document.body.style.paddingRight = '';
                            }, 150);
                        });
                    }
                    orderModal.show();
                };
                
                // Listen for the event to show modal
                @this.on('show-order-modal', () => {
                    // Small timeout to ensure DOM is updated
                    setTimeout(showOrderModal, 100);
                });
                
                // If there's already a selected order when component mounts, show modal
                if (@js($selectedOrder)) {
                    showOrderModal();
                }
                
                // Add click handlers to all order cards
                setupOrderCardClickHandlers();
                
                // Mark as initialized
                window.historyPageInitialized = true;
            } catch (error) {
                // Silent error handling
            }
        }
        
        // Function to set up click handlers on all order cards
        function setupOrderCardClickHandlers() {
            // Get all order cards with wire:click attribute
            const orderCards = document.querySelectorAll('.card[wire\\:click^="viewOrder"]');
            
            // Add click handlers to all cards
            orderCards.forEach(card => {
                // Extract order ID from the wire:click attribute
                const wireClickAttr = card.getAttribute('wire:click');
                const orderIdMatch = wireClickAttr.match(/viewOrder\(([0-9]+)\)/);
                
                if (orderIdMatch && orderIdMatch[1]) {
                    const orderId = orderIdMatch[1];
                    
                    // Add a direct click handler (as a backup)
                    card.addEventListener('click', function(e) {
                        // Call the Livewire method directly
                        @this.call('viewOrder', orderId);
                    });
                }
            });
        }
        
        // Initialize on different events to ensure it works in all scenarios
        document.addEventListener('DOMContentLoaded', initHistoryPage);
        document.addEventListener('livewire:initialized', initHistoryPage);
        
        // Initialize when Livewire navigates to this page
        document.addEventListener('livewire:navigated', () => {
            window.historyPageInitialized = false; // Reset to allow re-initialization
            initHistoryPage();
        });
        
        // Additional safety for Livewire updates
        document.addEventListener('livewire:update', function() {
            if (!window.historyPageInitialized) {
                initHistoryPage();
            } else {
                // Re-setup click handlers in case new order cards were added
                setupOrderCardClickHandlers();
            }
        });
    </script>
    @endpush
</div>
