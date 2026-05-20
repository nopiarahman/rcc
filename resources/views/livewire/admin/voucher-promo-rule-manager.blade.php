<div class="p-6 max-w-5xl mx-auto">
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
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Promo Voucher Otomatis</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Atur aturan voucher yang otomatis diberikan ke pelanggan setelah berbelanja
            </p>
        </div>
        <button wire:click="openCreate"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Aturan
        </button>
    </div>

    {{-- Info box --}}
    <div class="mb-6 rounded-lg bg-blue-50 border border-blue-200 px-4 py-3 text-blue-800 text-sm dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-300">
        <div class="font-semibold mb-1">Cara Kerja (Kumulatif)</div>
        <ul class="list-disc list-inside space-y-0.5 text-xs">
            <li>Voucher diberikan berdasarkan <strong>total belanja kumulatif</strong> dari semua order yang sudah selesai — tidak harus dalam satu order.</li>
            <li>Contoh: syarat Rp 100.000 → Order pertama Rp 60.000 + Order kedua Rp 50.000 = Rp 110.000 → voucher keluar saat order kedua selesai.</li>
            <li>Order yang sudah "menghitung" ke voucher sebelumnya tidak dihitung ulang.</li>
            <li>Voucher bisa dilihat dan dipakai di halaman akun pelanggan.</li>
        </ul>
    </div>

    {{-- Form Modal --}}
    @if($showForm)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5)">
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-zinc-700">
                <h3 class="font-bold text-gray-900 dark:text-white">
                    {{ $editingId ? 'Edit Aturan Promo' : 'Tambah Aturan Promo' }}
                </h3>
                <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-4 space-y-4">
                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Aturan</label>
                    <input type="text" wire:model="description"
                           class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white px-3 py-2 text-sm @error('description') border-red-400 @enderror"
                           placeholder="contoh: Belanja min 50rb dapat voucher 10rb">
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Min purchase --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Total Belanja Kumulatif untuk Dapat Voucher
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-semibold">Rp</span>
                        <input type="number" wire:model="min_purchase_amount"
                               class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white pl-10 pr-3 py-2 text-sm @error('min_purchase_amount') border-red-400 @enderror"
                               placeholder="50000">
                    </div>
                    @error('min_purchase_amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Voucher type + value --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Diskon Voucher</label>
                        <select wire:model="voucher_discount_type"
                                class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                            <option value="fixed">Nominal (Rp)</option>
                            <option value="percentage">Persen (%)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nilai Diskon {{ $voucher_discount_type === 'percentage' ? '(%)' : '(Rp)' }}
                        </label>
                        <input type="number" wire:model="voucher_discount_value"
                               class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white px-3 py-2 text-sm @error('voucher_discount_value') border-red-400 @enderror"
                               placeholder="{{ $voucher_discount_type === 'percentage' ? '10' : '10000' }}">
                        @error('voucher_discount_value')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Voucher min purchase + validity --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Min. Belanja untuk Pakai Voucher</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-semibold">Rp</span>
                            <input type="number" wire:model="voucher_min_purchase"
                                   class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white pl-10 pr-3 py-2 text-sm"
                                   placeholder="0">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">0 = tidak ada minimum</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Masa Berlaku (hari)</label>
                        <input type="number" wire:model="voucher_validity_days"
                               class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white px-3 py-2 text-sm"
                               placeholder="30">
                        @error('voucher_validity_days')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Active --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model="is_active" id="is_active_rule"
                           class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer">
                    <label for="is_active_rule" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                        Aktifkan aturan ini
                    </label>
                </div>

                {{-- Preview --}}
                @if($min_purchase_amount && $voucher_discount_value)
                <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm">
                    <div class="font-semibold text-green-800 dark:text-green-300 mb-1">Preview Aturan</div>
                    <div class="text-green-700 dark:text-green-400 text-xs">
                        Setiap akumulasi belanja <strong>Rp {{ number_format((int)$min_purchase_amount, 0, ',', '.') }}</strong>
                        (dari semua order selesai), pelanggan mendapat voucher diskon
                        <strong>{{ $voucher_discount_type === 'percentage' ? (int)$voucher_discount_value . '%' : 'Rp ' . number_format((int)$voucher_discount_value, 0, ',', '.') }}</strong>
                        @if($voucher_min_purchase > 0) (min. pakai Rp {{ number_format((int)$voucher_min_purchase, 0, ',', '.') }})@endif,
                        berlaku <strong>{{ $voucher_validity_days ?: 30 }} hari</strong>.
                    </div>
                </div>
                @endif
            </div>
            <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-700 flex gap-3 justify-end">
                <button wire:click="$set('showForm', false)"
                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-zinc-600 text-gray-700 dark:text-gray-300 text-sm hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                    Batal
                </button>
                <button wire:click="save"
                        class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-semibold transition-colors"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $editingId ? 'Simpan Perubahan' : 'Tambah Aturan' }}</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Rules Table --}}
    @if($rules->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
        </svg>
        <div class="font-medium text-gray-500 dark:text-gray-400">Belum ada aturan promo</div>
        <div class="text-sm mt-1">Klik "Tambah Aturan" untuk membuat promo voucher pertama</div>
    </div>
    @else
    <div class="space-y-3">
        @foreach($rules as $rule)
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm overflow-hidden">
            <div class="flex items-center gap-4 p-4">
                {{-- Status indicator --}}
                <div class="flex-shrink-0">
                    <div class="w-3 h-3 rounded-full {{ $rule->is_active ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                </div>

                {{-- Info --}}
                <div class="flex-grow min-w-0">
                    <div class="font-semibold text-gray-900 dark:text-white text-sm">{{ $rule->description }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex flex-wrap gap-x-3 gap-y-0.5">
                        <span>
                            <span class="font-medium">Akumulasi:</span> Rp {{ number_format($rule->min_purchase_amount, 0, ',', '.') }}
                        </span>
                        <span>
                            <span class="font-medium">Voucher:</span>
                            {{ $rule->voucher_discount_type === 'percentage' ? (int)$rule->voucher_discount_value . '%' : 'Rp ' . number_format($rule->voucher_discount_value, 0, ',', '.') }}
                        </span>
                        @if($rule->voucher_min_purchase > 0)
                        <span>
                            <span class="font-medium">Min. pakai:</span> Rp {{ number_format($rule->voucher_min_purchase, 0, ',', '.') }}
                        </span>
                        @endif
                        <span>
                            <span class="font-medium">Berlaku:</span> {{ $rule->voucher_validity_days }} hari
                        </span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex-shrink-0 flex items-center gap-2">
                    <button wire:click="toggleActive({{ $rule->id }})"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors
                                {{ $rule->is_active
                                    ? 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400'
                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-zinc-700 dark:text-gray-400' }}">
                        {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                    </button>
                    <button wire:click="openEdit({{ $rule->id }})"
                            class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button wire:click="delete({{ $rule->id }})"
                            wire:confirm="Hapus aturan ini?"
                            class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
