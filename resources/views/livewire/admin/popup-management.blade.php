<div class="max-w-6xl mx-auto p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Kelola Popup</h1>
            <p class="text-sm text-gray-500 mt-0.5">Notifikasi popup yang muncul saat customer membuka halaman menu</p>
        </div>
        <flux:button variant="primary" wire:click="openForm" icon="plus">Tambah Popup</flux:button>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
        <table class="min-w-full text-sm divide-y divide-gray-100 dark:divide-neutral-700">
            <thead class="bg-gray-50 dark:bg-neutral-900">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Preview</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Judul / Konten</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Tipe</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Periode</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Urutan</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-neutral-700">
                @forelse($popups as $popup)
                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition">
                        <td class="px-4 py-3">
                            @if($popup->type === 'image' && $popup->hasMedia('popups'))
                                <img src="{{ $popup->getFirstMediaUrl('popups', 'thumb') }}" class="h-14 w-20 object-cover rounded-lg" alt="popup">
                            @elseif($popup->type === 'text')
                                <div class="h-14 w-20 bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center rounded-lg text-blue-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                    </svg>
                                </div>
                            @else
                                <div class="h-14 w-20 bg-gray-100 dark:bg-neutral-700 flex items-center justify-center rounded-lg text-gray-400 text-xs">No Image</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800 dark:text-white">{{ $popup->title ?? '—' }}</div>
                            @if($popup->content)
                                <div class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ Str::limit($popup->content, 60) }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $popup->type === 'image' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $popup->type === 'image' ? 'Gambar' : 'Teks' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 hidden md:table-cell">
                            @if($popup->start_date || $popup->end_date)
                                {{ $popup->start_date?->format('d/m/Y') ?? '∞' }} – {{ $popup->end_date?->format('d/m/Y') ?? '∞' }}
                            @else
                                <span class="text-gray-400 italic">Selalu tampil</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $popup->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $popup->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $popup->order }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="editPopup({{ $popup->id }})"
                                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    x-data x-on:click="if(confirm('Hapus popup ini?')) $wire.deletePopup({{ $popup->id }})"
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
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="text-gray-400 text-sm">Belum ada popup. Klik "Tambah Popup" untuk mulai.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Form --}}
    <flux:modal wire:model="showForm" class="max-w-lg w-full">
        <flux:heading size="lg">{{ $editing ? 'Edit Popup' : 'Tambah Popup Baru' }}</flux:heading>

        <form wire:submit.prevent="savePopup" class="mt-4 space-y-4">
            @if($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <flux:field>
                <flux:label>Tipe Popup</flux:label>
                <flux:select wire:model.live="type">
                    <option value="text">Teks</option>
                    <option value="image">Gambar</option>
                </flux:select>
                <flux:error name="type" />
            </flux:field>

            <flux:field>
                <flux:label>Judul <span class="text-gray-400 text-xs">(opsional)</span></flux:label>
                <flux:input type="text" wire:model.defer="title" placeholder="Judul popup" />
                <flux:error name="title" />
            </flux:field>

            @if($type === 'text')
                <flux:field>
                    <flux:label>Konten</flux:label>
                    <flux:textarea wire:model.defer="content" placeholder="Isi teks popup..." rows="4" />
                    <flux:error name="content" />
                </flux:field>
            @else
                <flux:field>
                    <flux:label>Gambar {{ $editing ? '(kosongkan jika tidak diganti)' : '' }}</flux:label>
                    <flux:input type="file" wire:model="image" accept="image/*" />
                    <flux:error name="image" />
                    @if($editing)
                        @php $popup = $popups->firstWhere('id', $popupId); @endphp
                        @if($popup && $popup->hasMedia('popups'))
                            <img src="{{ $popup->getFirstMediaUrl('popups', 'thumb') }}" class="h-16 mt-2 rounded-lg" alt="preview">
                        @endif
                    @endif
                </flux:field>
            @endif

            <div class="grid grid-cols-2 gap-3">
                <flux:field>
                    <flux:label>Tanggal Mulai</flux:label>
                    <flux:input type="date" wire:model.defer="start_date" />
                    <flux:error name="start_date" />
                </flux:field>
                <flux:field>
                    <flux:label>Tanggal Selesai</flux:label>
                    <flux:input type="date" wire:model.defer="end_date" />
                    <flux:error name="end_date" />
                </flux:field>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.defer="is_active">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </flux:select>
                    <flux:error name="is_active" />
                </flux:field>
                <flux:field>
                    <flux:label>Urutan</flux:label>
                    <flux:input type="number" wire:model.defer="order" min="0" />
                    <flux:error name="order" />
                </flux:field>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button variant="ghost" wire:click="resetFields">Batal</flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $editing ? 'Perbarui' : 'Simpan' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
