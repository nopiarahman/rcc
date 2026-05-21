<div class="max-w-6xl mx-auto p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Data Customer</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar user terdaftar beserta riwayat pemesanan</p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Customer</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalCustomers) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Pesanan (Customer)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalOrdersFromCustomers) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 dark:bg-yellow-900/40 dark:text-yellow-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Pendapatan (Customer)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalRevenueFromCustomers, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($showDetail && $customer)
        {{-- Customer Detail View --}}
        <div>
            {{-- Back Button --}}
            <button wire:click="closeDetail" class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white mb-4 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke daftar customer
            </button>

            {{-- Customer Profile Card --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm p-5 mb-5">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400 text-xl font-bold">
                        {{ $customer->initials() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $customer->nama }}</h2>
                        <p class="text-sm text-gray-500">{{ $customer->no_hp }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Terdaftar {{ $customer->created_at->diffForHumans() }} &middot; {{ $customer->created_at->isoFormat('D MMMM YYYY') }}</p>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-center shrink-0">
                        <div class="bg-gray-50 dark:bg-neutral-700 rounded-lg px-4 py-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Pesanan</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $customer->pesanans_count ?? $customer->pesanans()->count() }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-neutral-700 rounded-lg px-4 py-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Belanja</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($customer->pesanans()->where('status', '!=', 'dibatalkan')->sum('total_harga'), 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-neutral-700 rounded-lg px-4 py-2 col-span-2 sm:col-span-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Voucher Aktif</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $customer->activeVouchers()->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order History --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-100 dark:border-neutral-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Riwayat Pesanan</h3>
                    <div class="relative w-56">
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="orderSearch" placeholder="Cari nomor pesanan..." class="w-full pl-8 pr-3 py-1.5 text-sm border border-neutral-200 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-indigo-400" />
                    </div>
                </div>

                @forelse($orders as $order)
                    <div class="border-b border-neutral-100 dark:border-neutral-700 last:border-0">
                        <div class="px-5 py-4">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-mono text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ $order->nomor_pesanan }}</span>
                                        @php
                                            $statusMap = [
                                                'menunggu_konfirmasi' => ['label' => 'Menunggu', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'],
                                                'selesai'             => ['label' => 'Selesai',  'class' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'],
                                                'dibatalkan'          => ['label' => 'Dibatalkan','class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'],
                                            ];
                                            $s = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => 'bg-gray-100 text-gray-600'];
                                        @endphp
                                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $s['class'] }}">{{ $s['label'] }}</span>
                                        @if($order->order_type)
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-neutral-700 text-gray-500 dark:text-gray-400">{{ $order->order_type === 'delivery' ? 'Delivery' : 'Takeaway' }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">{{ $order->created_at->isoFormat('D MMM YYYY, HH:mm') }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="font-semibold text-gray-900 dark:text-white text-sm">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                                    @if($order->discount_amount > 0)
                                        <p class="text-xs text-green-600 dark:text-green-400">Hemat Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Order Items --}}
                            <div class="mt-3 space-y-1">
                                @foreach($order->details as $detail)
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                        <span class="w-5 h-5 flex items-center justify-center bg-gray-100 dark:bg-neutral-700 rounded text-gray-600 dark:text-gray-300 font-medium shrink-0">{{ $detail->qty }}</span>
                                        <span>
                                            {{ $detail->nama_minuman ?: $detail->nama_makanan ?: ($detail->nama_botolan ?? '-') }}
                                            @if($detail->size)
                                                <span class="text-gray-400">&middot; {{ $detail->size }}</span>
                                            @endif
                                            @if($detail->gula)
                                                <span class="text-gray-400">&middot; {{ $detail->gula }}</span>
                                            @endif
                                        </span>
                                        <span class="ml-auto shrink-0">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-gray-400">
                        Tidak ada pesanan ditemukan.
                    </div>
                @endforelse

                @if($orders->hasPages())
                    <div class="px-5 py-3 border-t border-neutral-100 dark:border-neutral-700">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>

    @else
        {{-- Customer List --}}
        <div class="mb-4">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <flux:input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau nomor HP..." class="pl-9" />
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <table class="min-w-full text-sm divide-y divide-gray-100 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">No. HP</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Pesanan</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Total Belanja</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Terdaftar</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-neutral-700">
                    @forelse($customers as $cust)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400 text-xs font-bold">
                                        {{ $cust->initials() }}
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $cust->nama }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500 hidden sm:table-cell">{{ $cust->no_hp }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $cust->pesanans_count > 0 ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-500 dark:bg-neutral-700 dark:text-gray-400' }}">
                                    {{ $cust->pesanans_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300 font-medium hidden md:table-cell">
                                Rp {{ number_format($cust->total_belanja ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-xs hidden lg:table-cell">
                                {{ $cust->created_at->isoFormat('D MMM YYYY') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <flux:button size="sm" wire:click="viewDetail({{ $cust->id }})" icon="eye">
                                    Lihat
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400">
                                Tidak ada customer ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($customers->hasPages())
                <div class="px-4 py-3 border-t border-neutral-100 dark:border-neutral-700">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
