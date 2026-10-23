<div class="max-w-6xl mx-auto p-6">

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-800 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Data Customer</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola akun customer beserta riwayat pemesanannya</p>
        </div>
        <button wire:click="openCreate"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Customer
        </button>
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
                        <div class="mt-2">
                            <flux:button size="sm" wire:click="openEdit({{ $customer->id }})" icon="pencil-square">
                                Edit / Reset Password
                            </flux:button>
                        </div>
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
                                <div class="flex justify-end gap-1.5">
                                    <flux:button size="sm" wire:click="viewDetail({{ $cust->id }})" icon="eye">
                                        Lihat
                                    </flux:button>
                                    <flux:button size="sm" wire:click="openEdit({{ $cust->id }})" icon="pencil-square">
                                        Edit
                                    </flux:button>
                                    <flux:button size="sm" variant="danger" icon="trash"
                                                 wire:click="delete({{ $cust->id }})"
                                                 wire:confirm="Hapus customer {{ $cust->nama }}? Riwayat pesanan tetap tersimpan, tetapi voucher miliknya ikut terhapus.">
                                        Hapus
                                    </flux:button>
                                </div>
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

    {{-- Form Modal --}}
    @if($showForm)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5)">
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-zinc-700">
                <h3 class="font-bold text-gray-900 dark:text-white">
                    {{ $editingId ? 'Edit Customer' : 'Tambah Customer' }}
                </h3>
                <button wire:click="closeForm" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save">
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama</label>
                        <input type="text" wire:model="nama"
                               class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white px-3 py-2 text-sm @error('nama') border-red-400 @enderror"
                               placeholder="Nama customer">
                        @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor HP</label>
                        <input type="tel" wire:model="no_hp"
                               class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white px-3 py-2 text-sm @error('no_hp') border-red-400 @enderror"
                               placeholder="08xxxxxxxxxx">
                        <p class="text-xs text-gray-400 mt-1">Nomor ini dipakai customer untuk login. Awalan 0 otomatis disimpan sebagai 62.</p>
                        @error('no_hp')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ $editingId ? 'Password Baru' : 'Password' }}
                            </label>
                            <input type="password" wire:model="password" autocomplete="new-password"
                                   class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white px-3 py-2 text-sm @error('password') border-red-400 @enderror"
                                   placeholder="Minimal 6 karakter">
                            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konfirmasi Password</label>
                            <input type="password" wire:model="password_confirmation" autocomplete="new-password"
                                   class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white px-3 py-2 text-sm"
                                   placeholder="Ulangi password">
                        </div>
                    </div>
                    @if($editingId)
                        <p class="text-xs text-gray-400 -mt-2">Kosongkan password jika tidak ingin mengubahnya.</p>
                    @endif
                </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-700 flex gap-3 justify-end">
                    <button type="button" wire:click="closeForm"
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-zinc-600 text-gray-700 dark:text-gray-300 text-sm hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-semibold transition-colors"
                            wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save">{{ $editingId ? 'Simpan Perubahan' : 'Tambah Customer' }}</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
