<div class="pb-5">

    <style>
        .acct-header {
            background: #fff;
            border-bottom: 1px solid #f0f0f0;
        }
        .acct-avatar {
            width: 56px; height: 56px;
            border-radius: 50%;
            background: var(--button-bg-color, #006a3e);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; font-weight: 700; color: #fff;
        }
        .acct-tabs {
            background: #fff;
            border-bottom: 1px solid #f0f0f0;
        }
        .acct-tab-btn {
            background: none; border: none;
            color: #6b7280;
            font-size: 0.875rem; font-weight: 500;
            padding: 12px 20px;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
            cursor: pointer;
        }
        .acct-tab-btn.active {
            color: var(--button-bg-color, #006a3e);
            border-bottom-color: var(--button-bg-color, #006a3e);
            font-weight: 600;
        }
        .order-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 6px rgba(0,0,0,0.07);
            border: 1px solid #f0f0f0;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .order-card:active { transform: scale(0.99); }
        .order-status {
            font-size: 0.7rem; font-weight: 700;
            padding: 3px 10px; border-radius: 20px;
            text-transform: uppercase; letter-spacing: 0.05em;
        }
        .status-menunggu_konfirmasi { background:#fff3cd;color:#92660f; }
        .status-diproses { background:#cff4fc;color:#0b5e73; }
        .status-diantar, .status-dikirim { background:#d1ecf1;color:#0c5460; }
        .status-selesai { background:#d4edda;color:#155724; }
        .status-dibatalkan { background:#f8d7da;color:#721c24; }

        .voucher-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .voucher-card.used {
            opacity: 0.6;
        }
        .voucher-stripe {
            width: 6px;
            flex-shrink: 0;
        }
        .voucher-code-box {
            background: #f8f9fa;
            border: 1.5px dashed #dee2e6;
            border-radius: 8px;
            padding: 6px 12px;
            font-family: monospace;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.1em;
        }
        .copy-btn {
            border: none; background: none;
            font-size: 0.75rem; font-weight: 600;
            color: var(--button-bg-color, #006a3e);
            cursor: pointer; padding: 0;
            transition: opacity 0.15s;
        }
        .copy-btn:active { opacity: 0.6; }
        .empty-state { text-align: center; padding: 48px 24px; color: #9ca3af; }
        .empty-state i { font-size: 3rem; margin-bottom: 12px; opacity: 0.4; }

        /* Order detail modal */
        .od-item { padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
        .od-item:last-child { border-bottom: none; }
    </style>

    {{-- Sticky Header --}}
    <div class="acct-header sticky-top">
        <div class="container py-3">
            <div class="d-flex align-items-center gap-3">
                <div class="acct-avatar">{{ $customer->initials() }}</div>
                <div class="flex-grow-1">
                    <div class="fw-bold" style="font-size:1.05rem;line-height:1.3;color:#1a1a1a;">{{ $customer->nama }}</div>
                    <div class="text-muted small">{{ $customer->formatted_phone }}</div>
                </div>
                <button wire:click="logout"
                        class="btn btn-sm"
                        style="background:#f3f4f6;color:#374151;border-radius:8px;border:1px solid #e5e7eb;font-size:0.8rem;"
                        wire:confirm="Yakin ingin keluar?">
                    <i class="fas fa-sign-out-alt me-1"></i> Keluar
                </button>
            </div>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="acct-tabs">
        <div class="container d-flex">
            <button class="acct-tab-btn {{ $activeTab === 'history' ? 'active' : '' }}"
                    wire:click="setTab('history')">
                <i class="fas fa-receipt me-2"></i>Riwayat Belanja
            </button>
            <button class="acct-tab-btn {{ $activeTab === 'vouchers' ? 'active' : '' }}"
                    wire:click="setTab('vouchers')">
                <i class="fas fa-ticket-alt me-2"></i>Voucher
                @if($activeVouchers->count() > 0)
                    <span class="badge rounded-pill ms-1 text-white" style="background:var(--button-bg-color,#006a3e);font-size:0.65rem;">
                        {{ $activeVouchers->count() }}
                    </span>
                @endif
            </button>
        </div>
    </div>

    {{-- Content --}}
    <div class="container py-4">

        {{-- ========== HISTORY TAB ========== --}}
        @if($activeTab === 'history')
            @if($orders->isEmpty())
                <div class="empty-state">
                    <div><i class="fas fa-shopping-bag"></i></div>
                    <div class="fw-semibold mb-1" style="font-size:1.05rem;color:#374151;">Belum ada pesanan</div>
                    <div class="text-muted small mb-3">Pesanan yang kamu buat akan muncul di sini</div>
                    <a href="{{ route('home') }}" wire:navigate class="btn btn-sm fw-semibold px-4 text-white"
                       style="background:var(--button-bg-color,#006a3e);border-radius:20px;">
                        Lihat Menu
                    </a>
                </div>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach($orders as $order)
                    @php
                        $statusLabel = [
                            'menunggu_konfirmasi' => 'Menunggu',
                            'diproses' => 'Diproses',
                            'diantar' => 'Diantar',
                            'dikirim' => 'Dikirim',
                            'selesai' => 'Selesai',
                            'dibatalkan' => 'Dibatalkan',
                        ][$order->status] ?? $order->status;
                        $statusIcon = [
                            'menunggu_konfirmasi' => 'fa-clock',
                            'diproses' => 'fa-fire',
                            'diantar' => 'fa-motorcycle',
                            'dikirim' => 'fa-shipping-fast',
                            'selesai' => 'fa-check-circle',
                            'dibatalkan' => 'fa-times-circle',
                        ][$order->status] ?? 'fa-circle';
                        $itemNames = $order->details->map(function($d) {
                            return $d->nama_minuman ?? $d->nama_makanan ?? $d->nama_botolan ?? '-';
                        })->filter()->take(3)->implode(', ');
                        $moreCount = max(0, $order->details->count() - 3);
                    @endphp
                    <div class="order-card" wire:click="viewOrder({{ $order->id }})" style="cursor:pointer;">
                        <div class="p-3">
                            {{-- Row 1: Status + Tanggal --}}
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted" style="font-size:0.75rem;">
                                    <i class="far fa-clock me-1"></i>{{ $order->created_at->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                                </span>
                                <span class="order-status status-{{ $order->status }}">
                                    <i class="fas {{ $statusIcon }} me-1"></i>{{ $statusLabel }}
                                </span>
                            </div>

                            {{-- Row 2: Nama item --}}
                            <div class="fw-semibold mb-1" style="color:#1a1a1a;font-size:0.92rem;
                                display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.4;">
                                {{ $itemNames }}{{ $moreCount > 0 ? " +{$moreCount} lainnya" : '' }}
                            </div>

                            {{-- Row 3: Total + meta --}}
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <div class="d-flex align-items-center gap-2 text-muted" style="font-size:0.78rem;">
                                    <span>{{ $order->details->count() }} item</span>
                                    <span>•</span>
                                    <span>
                                        <i class="fas {{ $order->order_type === 'delivery' ? 'fa-motorcycle' : 'fa-store' }} me-1"></i>
                                        {{ $order->order_type === 'delivery' ? 'Antar' : 'Ambil' }}
                                    </span>
                                </div>
                                <div class="fw-bold" style="color:var(--button-bg-color,#006a3e);font-size:1rem;white-space:nowrap;">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($orders->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
                @endif
            @endif
        @endif

        {{-- ========== VOUCHERS TAB ========== --}}
        @if($activeTab === 'vouchers')

            @if($activeVouchers->isEmpty() && $usedVouchers->isEmpty())
                <div class="empty-state">
                    <div><i class="fas fa-ticket-alt"></i></div>
                    <div class="fw-semibold mb-1" style="font-size:1.05rem;color:#374151;">Belum ada voucher</div>
                    <div class="text-muted small mb-3">Belanja lebih banyak untuk mendapatkan voucher diskon!</div>
                    <a href="{{ route('home') }}" wire:navigate class="btn btn-sm fw-semibold px-4 text-white"
                       style="background:var(--button-bg-color,#006a3e);border-radius:20px;">
                        Belanja Sekarang
                    </a>
                </div>
            @else
                {{-- Active Vouchers --}}
                @if($activeVouchers->isNotEmpty())
                <div class="mb-2 text-muted small fw-semibold px-1">
                    <i class="fas fa-fire-alt me-1" style="color:#fbbf24;"></i>
                    BISA DIGUNAKAN ({{ $activeVouchers->count() }})
                </div>
                <div class="d-flex flex-column gap-3 mb-4">
                    @foreach($activeVouchers as $voucher)
                    @php
                        $isPercentage = $voucher->discount_type === 'percentage';
                        $stripeColor = 'var(--button-bg-color, #006a3e)';
                    @endphp
                    <div class="voucher-card d-flex">
                        <div class="voucher-stripe" style="background:{{ $stripeColor }};"></div>
                        <div class="flex-grow-1 p-3">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <div>
                                    <div class="fw-bold" style="font-size:1.25rem;color:var(--button-bg-color,#006a3e);">
                                        @if($isPercentage)
                                            Diskon {{ (int)$voucher->discount_value }}%
                                        @else
                                            Diskon Rp {{ number_format($voucher->discount_value, 0, ',', '.') }}
                                        @endif
                                    </div>
                                    @if($voucher->description)
                                        <div class="text-muted small">{{ $voucher->description }}</div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    @if($voucher->expires_at)
                                    @php $daysLeft = now()->diffInDays($voucher->expires_at, false); @endphp
                                    <div class="small {{ $daysLeft <= 3 ? 'text-danger fw-semibold' : 'text-muted' }}">
                                        @if($daysLeft <= 0)
                                            <i class="fas fa-exclamation-triangle"></i> Hari ini!
                                        @elseif($daysLeft <= 3)
                                            <i class="fas fa-clock"></i> {{ $daysLeft }}h lagi
                                        @else
                                            s.d. {{ $voucher->expires_at->format('d M Y') }}
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>

                            @if($voucher->min_purchase > 0)
                            <div class="text-muted small mb-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Min. belanja Rp {{ number_format($voucher->min_purchase, 0, ',', '.') }}
                            </div>
                            @endif

                            <div class="d-flex align-items-center gap-2">
                                <div class="voucher-code-box flex-grow-1 text-center">{{ $voucher->code }}</div>
                                <button class="copy-btn d-flex align-items-center gap-1 px-3 py-2"
                                        style="background:rgba(0,106,62,0.08);border-radius:8px;"
                                        wire:click="copyVoucher('{{ $voucher->code }}')"
                                        x-data
                                        @copy-to-clipboard.window="
                                            if($event.detail.code === '{{ $voucher->code }}') {
                                                navigator.clipboard.writeText('{{ $voucher->code }}');
                                                $el.innerHTML = '<i class=\'fas fa-check\'></i>';
                                                setTimeout(() => $el.innerHTML = '<i class=\'fas fa-copy\'></i> Salin', 1500);
                                            }
                                        ">
                                    <i class="fas fa-copy"></i> Salin
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Used / Expired Vouchers --}}
                @if($usedVouchers->isNotEmpty())
                <div class="mb-2 text-muted small fw-semibold px-1">
                    SUDAH DIGUNAKAN / KADALUARSA ({{ $usedVouchers->count() }})
                </div>
                <div class="d-flex flex-column gap-2">
                    @foreach($usedVouchers as $voucher)
                    <div class="voucher-card used d-flex">
                        <div class="voucher-stripe" style="background:#adb5bd;"></div>
                        <div class="flex-grow-1 p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-semibold text-muted">
                                        @if($voucher->discount_type === 'percentage')
                                            Diskon {{ (int)$voucher->discount_value }}%
                                        @else
                                            Diskon Rp {{ number_format($voucher->discount_value, 0, ',', '.') }}
                                        @endif
                                    </div>
                                    <div class="small text-muted">
                                        {{ $voucher->is_used ? 'Digunakan ' . $voucher->used_at?->locale('id')->diffForHumans() : 'Kadaluarsa' }}
                                    </div>
                                </div>
                                <div class="voucher-code-box text-muted" style="font-size:0.85rem;">{{ $voucher->code }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            @endif
        @endif
    </div>

    {{-- ========== ORDER DETAIL MODAL ========== --}}
    @if($selectedOrder)
    <div class="modal fade" id="orderDetailModal" tabindex="-1"
         x-data
         @show-order-detail.window="bootstrap.Modal.getOrCreateInstance($el).show()"
         wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border-radius:20px;border:none;">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h6 class="fw-bold mb-0" style="color:#1a1a1a;">Detail Pesanan</h6>
                        <div class="text-muted" style="font-size:0.75rem;font-family:monospace;">{{ $selectedOrder->nomor_pesanan }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeOrder"></button>
                </div>
                <div class="modal-body pt-2">
                    @php
                        $sl = [
                            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                            'diproses' => 'Diproses',
                            'diantar' => 'Diantar',
                            'dikirim' => 'Dikirim',
                            'selesai' => 'Selesai',
                            'dibatalkan' => 'Dibatalkan',
                        ][$selectedOrder->status] ?? $selectedOrder->status;
                    @endphp
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-muted small">
                            <i class="far fa-calendar me-1"></i>
                            {{ $selectedOrder->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}
                        </div>
                        <span class="order-status status-{{ $selectedOrder->status }}">{{ $sl }}</span>
                    </div>

                    {{-- Info pengiriman --}}
                    <div class="rounded-3 p-3 mb-3" style="background:#f8f9fa;">
                        <div class="small text-muted mb-1 fw-semibold">INFO PESANAN</div>
                        <div class="small mb-1"><span class="text-muted">Nama:</span> <span class="fw-semibold">{{ $selectedOrder->nama_pemesan }}</span></div>
                        <div class="small mb-1">
                            <span class="text-muted">Jenis:</span>
                            <span class="fw-semibold">{{ $selectedOrder->order_type === 'delivery' ? 'Antar ke Alamat' : 'Ambil Sendiri' }}</span>
                        </div>
                        @if($selectedOrder->order_type === 'delivery' && $selectedOrder->alamat_pengantaran !== 'Takeaway')
                        <div class="small mb-1"><span class="text-muted">Alamat:</span> <span class="fw-semibold">{{ $selectedOrder->alamat_pengantaran }}</span></div>
                        @endif
                        @if($selectedOrder->waktu_pengantaran)
                        <div class="small"><span class="text-muted">Waktu:</span> <span class="fw-semibold">{{ $selectedOrder->waktu_pengantaran }}</span></div>
                        @endif
                    </div>

                    {{-- Items --}}
                    <div class="mb-3">
                        <div class="small text-muted mb-2 fw-semibold">ITEM PESANAN</div>
                        @foreach($selectedOrder->details as $detail)
                        <div class="od-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1 me-2">
                                    <div class="fw-semibold" style="font-size:0.9rem;color:#1a1a1a;">
                                        {{ $detail->nama_minuman ?? $detail->nama_makanan ?? $detail->nama_botolan }}
                                        <span class="text-muted fw-normal"> ×{{ $detail->qty }}</span>
                                    </div>
                                    @if($detail->size || $detail->gula || $detail->topping)
                                    <div class="text-muted" style="font-size:0.77rem;">
                                        {{ collect([$detail->size, $detail->gula ? 'Gula: '.$detail->gula : null, $detail->topping ? 'Topping: '.$detail->topping : null])->filter()->implode(' · ') }}
                                    </div>
                                    @endif
                                    @if($detail->catatan)
                                    <div class="text-muted fst-italic" style="font-size:0.77rem;">Catatan: {{ $detail->catatan }}</div>
                                    @endif
                                </div>
                                <div class="fw-semibold text-end" style="font-size:0.88rem;color:#1a1a1a;white-space:nowrap;">
                                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Summary --}}
                    <div class="rounded-3 p-3" style="background:#f8f9fa;">
                        @php $subtotal = $selectedOrder->details->sum('subtotal'); @endphp
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($selectedOrder->discount_amount > 0)
                        <div class="d-flex justify-content-between small mb-1 text-success">
                            <span>Diskon kode</span>
                            <span>-Rp {{ number_format($selectedOrder->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if(($selectedOrder->voucher_amount ?? 0) > 0)
                        <div class="d-flex justify-content-between small mb-1 text-success">
                            <span>Voucher</span>
                            <span>-Rp {{ number_format($selectedOrder->voucher_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if($selectedOrder->ongkir > 0)
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Ongkir</span>
                            <span>+Rp {{ number_format($selectedOrder->ongkir, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total</span>
                            <span style="color:var(--button-bg-color,#006a3e);">Rp {{ number_format($selectedOrder->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Toast notification for copy --}}
    <div x-data="{ show: false, msg: '' }"
         @copy-to-clipboard.window="msg = 'Kode disalin!'; show = true; setTimeout(() => show = false, 2000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-200"
         x-transition:leave="transition ease-in duration-150"
         style="position:fixed;bottom:90px;left:50%;transform:translateX(-50%);z-index:9999;
                background:rgba(0,0,0,0.75);color:#fff;padding:8px 20px;border-radius:20px;
                font-size:0.85rem;white-space:nowrap;pointer-events:none;"
         x-cloak>
        <i class="fas fa-check me-1"></i> <span x-text="msg"></span>
    </div>
</div>
