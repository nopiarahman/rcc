<div class="container p-4 bg-white mb-5" style="min-height: 100vh; display: flex; flex-direction: column;">
    
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
            <h5 class="fw-bold pt-3 pe-2 mb-0" style="font-size: 1.1rem;">Riwayat Pesanan</h5>
        </div>
        <p class="small text-gray-400 mb-4" style="text-align: right;">Klik pesanan untuk melihat lebih detail</p>

        @if($orders->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-history fa-3x text-muted"></i>
                </div>
                <h5 class="mb-2">Belum ada riwayat pesanan</h5>
                <p class="text-gray-400 mb-4">Pesanan yang Anda buat akan muncul di sini</p>
                <a href="{{ route('home') }}" class="inline-block bg-primary text-white px-6 py-2 rounded shadow hover:bg-blue-600 transition">
                    <i class="fas fa-utensils mr-2"></i>Pesan Sekarang
                </a>
            </div>
        @else
            <div>
                @foreach($orders as $order)
                    <div class="card mb-2 rounded-3 btn btn-light" style="text-align: left;" wire:click="viewOrder({{ $order->id }})">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-bold text-success">
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
                    <h5 class="modal-title">Detail Pesanan</h5>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Initialize modal variable
            let orderModal = null;
            
            // Function to show modal
            const showOrderModal = () => {
                if (!orderModal) {
                    const modalEl = document.getElementById('orderDetailModal');
                    orderModal = new bootstrap.Modal(modalEl);
                    
                    // Clean up modal instance when hidden
                    modalEl.addEventListener('hidden.bs.modal', function () {
                        @this.set('selectedOrder', null, false);
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
        });
        
        // Handle page navigation with Livewire
        document.addEventListener('livewire:navigated', () => {
            if (@js($selectedOrder)) {
                const modalEl = document.getElementById('orderDetailModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modal.show();
                }
            }
        });
    </script>
    @endpush
</div>
