<div class="container p-4 bg-white mb-5" style="min-height: 100vh; display: flex; flex-direction: column;">
    {{-- Header mirip cart-page --}}
    <div class="container py-4">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('home') }}" class="text-dark me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h4 class="mb-0">Riwayat Pesanan</h4>
        </div>

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
            <div style="padding-bottom:100px;">
                @foreach($orders as $order)
                    <div class="card mb-3 border-0 shadow-sm rounded-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-bold text-success">
                                    <span class="text-muted small">{{ $order->created_at->format('d M Y H:i') }}</span>
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
                                <span class="fw-semibold">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                            </div>
                            <button class="btn btn-link p-0 text-primary small" wire:click="viewOrder({{ $order->id }})">
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

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
                            <p class="mb-1"><strong>No. Telepon:</strong> {{ $selectedOrder->nomor_telepon }}</p>
                            <p class="mb-1"><strong>Catatan:</strong> {{ $selectedOrder->catatan ?? '-' }}</p>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('show-order-modal', () => {
                const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
                modal.show();
            });
        });
    </script>
    @endpush
</div>
