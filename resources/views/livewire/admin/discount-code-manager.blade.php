<div class="max-w-6xl mx-auto p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Kode Diskon</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kode voucher yang diinput customer saat checkout</p>
        </div>
        <flux:button variant="primary" wire:click="openForm" icon="plus">Tambah Kode</flux:button>
    </div>

    @if(session()->has('success'))
        <div class="flex items-center gap-2 p-3 mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="flex items-center gap-2 p-3 mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <div class="flex-1 relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <flux:input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kode diskon..." class="pl-9" />
        </div>
        <flux:select wire:model.live="filterActive" class="sm:w-48">
            <flux:select.option value="all">Semua</flux:select.option>
            <flux:select.option value="active">Sedang Aktif</flux:select.option>
            <flux:select.option value="inactive">Nonaktif</flux:select.option>
        </flux:select>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
        <table class="min-w-full text-sm divide-y divide-gray-100 dark:divide-neutral-700">
            <thead class="bg-gray-50 dark:bg-neutral-900">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kode</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Potongan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Min. Belanja</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Pemakaian</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Periode</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-neutral-700">
                @forelse($discountCodes as $code)
                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition">
                        <td class="px-4 py-3">
                            <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 px-2 py-0.5 rounded text-sm">
                                {{ $code->code }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800 dark:text-white">{{ $code->name }}</div>
                            @if($code->description)
                                <div class="text-xs text-gray-400 mt-0.5">{{ Str::limit($code->description, 40) }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-orange-600">{{ $code->formatted_discount }}</span>
                            <div class="text-xs text-gray-400">{{ $code->discount_type === 'percentage' ? 'Persentase' : 'Nominal' }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 hidden lg:table-cell">
                            Rp {{ number_format($code->minimum_purchase, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center hidden md:table-cell">
                            <div class="text-gray-800 dark:text-white font-medium">{{ $code->used_count }}×</div>
                            <div class="text-xs text-gray-400">
                                @if($code->max_redeem)
                                    sisa {{ $code->remainingUses }}
                                @else
                                    unlimited
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 hidden lg:table-cell">
                            <div>{{ $code->start_date->format('d M Y') }}</div>
                            <div class="text-gray-400">s/d {{ $code->end_date->format('d M Y') }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $code->isActive() ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $code->isActive() ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="toggleActive({{ $code->id }})"
                                    class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition"
                                    title="{{ $code->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                </button>
                                <button wire:click="duplicate({{ $code->id }})"
                                    class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/30 rounded-lg transition" title="Duplikat">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <button wire:click="edit({{ $code->id }})"
                                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="confirmDelete({{ $code->id }})"
                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400 text-sm">
                            Belum ada kode diskon.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100 dark:border-neutral-700">
            {{ $discountCodes->links() }}
        </div>
    </div>

    {{-- Modal Form --}}
    <flux:modal title="{{ $editMode ? 'Edit Kode Diskon' : 'Tambah Kode Diskon Baru' }}" wire:model="showForm">
        <form wire:submit.prevent="save" class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <flux:field>
                    <flux:label>Kode Voucher</flux:label>
                    <div class="flex gap-2">
                        <flux:input type="text" wire:model="code" placeholder="PROMO2025" class="flex-1 font-mono uppercase" />
                        <flux:button type="button" wire:click="generateCode" variant="outline" size="sm">Auto</flux:button>
                    </div>
                    @error('code') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
                <flux:field>
                    <flux:label>Nama Kode</flux:label>
                    <flux:input type="text" wire:model="name" placeholder="Promo Lebaran 2025" />
                    @error('name') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Deskripsi <span class="text-gray-400 text-xs">(opsional)</span></flux:label>
                <flux:textarea wire:model="description" rows="2" placeholder="Keterangan singkat" />
            </flux:field>

            <div class="grid grid-cols-2 gap-3">
                <flux:field>
                    <flux:label>Besar Potongan</flux:label>
                    <flux:input type="number" wire:model="discount_amount" min="0" step="0.01" />
                    @error('discount_amount') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
                <flux:field>
                    <flux:label>Tipe Potongan</flux:label>
                    <flux:select wire:model="discount_type">
                        <flux:select.option value="percentage">Persentase (%)</flux:select.option>
                        <flux:select.option value="fixed">Nominal (Rp)</flux:select.option>
                    </flux:select>
                </flux:field>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <flux:field>
                    <flux:label>Min. Belanja (Rp)</flux:label>
                    <flux:input type="number" wire:model="minimum_purchase" min="0" placeholder="0 = tanpa minimum" />
                    @error('minimum_purchase') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
                <flux:field>
                    <flux:label>Maks. Penggunaan</flux:label>
                    <flux:input type="number" wire:model="max_redeem" min="1" placeholder="Kosong = unlimited" />
                    @error('max_redeem') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <flux:field>
                    <flux:label>Tanggal Mulai</flux:label>
                    <flux:input type="datetime-local" wire:model="start_date" />
                    @error('start_date') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
                <flux:field>
                    <flux:label>Tanggal Selesai</flux:label>
                    <flux:input type="datetime-local" wire:model="end_date" />
                    @error('end_date') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model="is_active" class="rounded text-blue-600">
                <span class="text-sm text-gray-700 dark:text-gray-300">Aktifkan kode ini</span>
            </label>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button variant="ghost" wire:click="closeForm">Batal</flux:button>
                <flux:button type="submit" variant="primary">{{ $editMode ? 'Perbarui' : 'Simpan' }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Delete Confirmation --}}
    <flux:modal title="Hapus Kode Diskon?" wire:model="showDeleteConfirmation">
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Kode diskon ini akan dihapus permanen dan tidak dapat dikembalikan.</p>
        <div class="flex justify-end gap-2">
            <flux:button variant="ghost" wire:click="cancelDelete">Batal</flux:button>
            <flux:button variant="danger" wire:click="delete">Hapus</flux:button>
        </div>
    </flux:modal>
</div>
