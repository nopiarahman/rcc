<div class="container py-4" style="max-width: 560px;">
    @php
        $themeColor = $web_settings->themeColor->button_bg_color ?? '#006a3e';
        $rp = fn ($v) => 'Rp' . number_format((float) $v, 0, ',', '.');
    @endphp

    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
             style="width:72px;height:72px;background:#dcfce7;color:#166534;font-size:2rem;">
            <i class="fas fa-check"></i>
        </div>
        <h1 class="fw-bold mb-1" style="font-size:1.4rem;">Pesanan Diterima!</h1>
        <p class="text-muted mb-2" style="font-size:.9rem;">Simpan nomor pesanan berikut untuk mengecek status pesanan Anda.</p>
        <div class="d-inline-block px-3 py-2 rounded-3 fw-bold" style="background:#f1f5f9;font-family:monospace;font-size:1rem;letter-spacing:.03em;">
            {{ $pesanan->nomor_pesanan }}
        </div>
    </div>

    @if($whatsappUrl)
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
           class="btn w-100 d-flex align-items-center justify-content-center gap-2 mb-2"
           style="background:#16a34a;color:#fff;border-radius:12px;padding:.8rem;font-weight:600;">
            <i class="fab fa-whatsapp" style="font-size:1.25rem;"></i> Kirim Pesanan via WhatsApp
        </a>
        <p class="text-muted text-center mb-4" style="font-size:.78rem;">
            Kirim pesan ini agar pesanan Anda segera kami proses. Pesanan sudah tercatat di sistem kami.
        </p>
    @else
        <div class="alert alert-warning text-center mb-4" style="font-size:.85rem;">
            Pesanan sudah tercatat. Nomor WhatsApp toko belum diatur, silakan hubungi toko secara langsung.
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-3" style="border-radius:14px;">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2" style="font-size:.85rem;">
                <span class="text-muted">Jenis Pesanan</span>
                <span class="fw-semibold">{{ $pesanan->order_type === 'delivery' ? 'Antar ke Alamat' : 'Ambil Sendiri' }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2" style="font-size:.85rem;">
                <span class="text-muted">Waktu {{ $pesanan->order_type === 'delivery' ? 'Pengantaran' : 'Pengambilan' }}</span>
                <span class="fw-semibold">{{ $pesanan->waktu_pengantaran ?: '-' }}</span>
            </div>
            @if($pesanan->order_type === 'delivery')
                <div class="mb-2" style="font-size:.85rem;">
                    <span class="text-muted d-block">Alamat</span>
                    <span>{{ $pesanan->alamat_pengantaran }}</span>
                </div>
            @endif
            <hr class="my-2">
            @foreach($pesanan->details as $item)
                <div class="d-flex justify-content-between mb-1" style="font-size:.85rem;">
                    <span>
                        {{ $item->qty }}× {{ $item->nama_minuman ?: $item->nama_makanan ?: $item->nama_botolan }}
                        @php $opsi = array_filter([$item->nama_minuman ? $item->size : null, $item->gula, $item->topping]); @endphp
                        @if($opsi)
                            <span class="text-muted d-block" style="font-size:.75rem;">{{ implode(' · ', $opsi) }}</span>
                        @endif
                    </span>
                    <span>{{ $rp($item->subtotal) }}</span>
                </div>
            @endforeach
            <hr class="my-2">
            @if($pesanan->discount_amount > 0)
                <div class="d-flex justify-content-between mb-1" style="font-size:.85rem;color:#166534;">
                    <span>Diskon{{ $pesanan->discountCode ? ' (' . $pesanan->discountCode->code . ')' : '' }}</span>
                    <span>-{{ $rp($pesanan->discount_amount) }}</span>
                </div>
            @endif
            @if($pesanan->voucher_amount > 0)
                <div class="d-flex justify-content-between mb-1" style="font-size:.85rem;color:#166534;">
                    <span>Voucher{{ $pesanan->voucher ? ' (' . $pesanan->voucher->code . ')' : '' }}</span>
                    <span>-{{ $rp($pesanan->voucher_amount) }}</span>
                </div>
            @endif
            @if($pesanan->ongkir > 0)
                <div class="d-flex justify-content-between mb-1" style="font-size:.85rem;">
                    <span>Ongkir</span>
                    <span>+{{ $rp($pesanan->ongkir) }}</span>
                </div>
            @endif
            <div class="d-flex justify-content-between fw-bold mt-1">
                <span>Total</span>
                <span style="color:{{ $themeColor }};">{{ $rp($pesanan->total) }}</span>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('pesanan') }}" wire:navigate class="btn btn-outline-secondary flex-fill" style="border-radius:12px;padding:.65rem;">
            Lihat Status Pesanan
        </a>
        <a href="{{ route('home') }}" wire:navigate class="btn flex-fill" style="border-radius:12px;padding:.65rem;background:{{ $themeColor }};color:#fff;">
            Kembali ke Menu
        </a>
    </div>
</div>
