<div class="mt-6 space-y-6">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pesanan Hari Ini</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $todayOrders }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Omzet Hari Ini</p>
            <p class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">
                Rp {{ number_format($todayRevenue, 0, ',', '.') }}
            </p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pesanan Bulan Ini</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $monthOrders }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Omzet Bulan Ini</p>
            <p class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">
                Rp {{ number_format($monthRevenue, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid md:grid-cols-3 gap-4">
        {{-- Revenue Bar Chart --}}
        <div class="md:col-span-2 rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Omzet 7 Hari Terakhir</h3>
            <div wire:ignore>
                <canvas id="revenueChart" height="120"></canvas>
            </div>
        </div>

        {{-- Status Doughnut --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Status Pesanan</h3>
            <div wire:ignore>
                <canvas id="statusChart" height="160"></canvas>
            </div>
            <div class="mt-3 space-y-1">
                @php
                    $statusLabels = [
                        'menunggu_konfirmasi' => ['label' => 'Menunggu', 'color' => '#f59e0b'],
                        'diproses'            => ['label' => 'Diproses', 'color' => '#3b82f6'],
                        'diantar'             => ['label' => 'Diantar',  'color' => '#8b5cf6'],
                        'dikirim'             => ['label' => 'Dikirim',  'color' => '#06b6d4'],
                        'selesai'             => ['label' => 'Selesai',  'color' => '#22c55e'],
                        'dibatalkan'          => ['label' => 'Batal',    'color' => '#ef4444'],
                    ];
                @endphp
                @foreach($statusLabels as $key => $meta)
                    @if(($statusCounts[$key] ?? 0) > 0)
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1.5">
                                <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:{{ $meta['color'] }}"></span>
                                <span class="text-gray-600 dark:text-gray-400">{{ $meta['label'] }}</span>
                            </div>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $statusCounts[$key] }}</span>
                        </div>
                    @endif
                @endforeach
                @if($statusCounts->isEmpty())
                    <p class="text-xs text-gray-400 text-center">Belum ada pesanan</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Top Products --}}
    <div class="grid md:grid-cols-2 gap-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Minuman Terlaris</h3>
            @if($topMinuman->isEmpty())
                <p class="text-sm text-gray-400">Belum ada data.</p>
            @else
                <div class="space-y-2">
                    @foreach($topMinuman as $i => $item)
                        @php $pct = $topMinuman->first()->total_qty > 0 ? ($item->total_qty / $topMinuman->first()->total_qty * 100) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-0.5">
                                <span class="text-gray-700 dark:text-gray-300 truncate max-w-[70%]">{{ $loop->iteration }}. {{ $item->nama_minuman }}</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $item->total_qty }}x</span>
                            </div>
                            <div class="h-1.5 w-full bg-gray-100 dark:bg-neutral-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-blue-500" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Makanan Terlaris</h3>
            @if($topMakanan->isEmpty())
                <p class="text-sm text-gray-400">Belum ada data.</p>
            @else
                <div class="space-y-2">
                    @foreach($topMakanan as $item)
                        @php $pct = $topMakanan->first()->total_qty > 0 ? ($item->total_qty / $topMakanan->first()->total_qty * 100) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-0.5">
                                <span class="text-gray-700 dark:text-gray-300 truncate max-w-[70%]">{{ $loop->iteration }}. {{ $item->nama_makanan }}</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $item->total_qty }}x</span>
                            </div>
                            <div class="h-1.5 w-full bg-gray-100 dark:bg-neutral-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-orange-400" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@once
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endonce

<script>
(function () {
    var labels  = @json($chartLabels);
    var orders  = @json($chartOrders);
    var revenue = @json($chartRevenue);
    var statusLabels = @json(array_values(array_map(fn($v) => $v['label'], $statusLabels)));
    var statusColors = @json(array_values(array_map(fn($v) => $v['color'], $statusLabels)));
    var statusKeys   = @json(array_keys($statusLabels));
    var statusCounts = @json($statusCounts);

    function initCharts() {
        var revenueCtx = document.getElementById('revenueChart');
        var statusCtx  = document.getElementById('statusChart');
        if (!revenueCtx || !statusCtx) return;

        if (revenueCtx._chartInstance) revenueCtx._chartInstance.destroy();
        if (statusCtx._chartInstance)  statusCtx._chartInstance.destroy();

        revenueCtx._chartInstance = new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Omzet (Rp)',
                    data: revenue,
                    backgroundColor: 'rgba(34,197,94,0.7)',
                    borderColor: 'rgba(34,197,94,1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return 'Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) {
                                if (v >= 1000000) return 'Rp ' + (v/1000000).toFixed(1) + 'jt';
                                if (v >= 1000) return 'Rp ' + (v/1000).toFixed(0) + 'rb';
                                return 'Rp ' + v;
                            }
                        }
                    }
                }
            }
        });

        var sData   = statusKeys.map(function(k) { return statusCounts[k] || 0; });
        var sColors = statusColors;
        var sLabels = statusLabels;
        var nonZeroData   = [], nonZeroColors = [], nonZeroLabels = [];
        sData.forEach(function(v, i) {
            if (v > 0) { nonZeroData.push(v); nonZeroColors.push(sColors[i]); nonZeroLabels.push(sLabels[i]); }
        });

        statusCtx._chartInstance = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: nonZeroLabels.length ? nonZeroLabels : ['Belum ada'],
                datasets: [{
                    data: nonZeroData.length ? nonZeroData : [1],
                    backgroundColor: nonZeroColors.length ? nonZeroColors : ['#e5e7eb'],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: { legend: { display: false } }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCharts);
    } else {
        initCharts();
    }
})();
</script>
