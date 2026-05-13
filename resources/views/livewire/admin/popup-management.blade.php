<div class="max-w-5xl mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4 mt-5 dark:text-white">
        {{ $editing ? 'Edit Popup' : 'Tambah Popup Baru' }}
    </h2>

    <div class="max-w-lg">
        @if($errors->any())
            <div class="p-4 mb-4 text-red-700 bg-red-100 rounded">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form wire:submit.prevent="savePopup">
            <flux:field class="mb-3">
                <flux:label>Tipe Popup</flux:label>
                <flux:select wire:model.live="type">
                    <option value="text">Teks</option>
                    <option value="image">Gambar</option>
                </flux:select>
                <flux:error name="type" />
            </flux:field>

            <flux:field class="mb-3">
                <flux:label>Judul (opsional)</flux:label>
                <flux:input type="text" wire:model.defer="title" placeholder="Judul popup" />
                <flux:error name="title" />
            </flux:field>

            @if($type === 'text')
                <flux:field class="mb-3">
                    <flux:label>Konten</flux:label>
                    <flux:textarea wire:model.defer="content" placeholder="Isi teks popup..." rows="4" />
                    <flux:error name="content" />
                </flux:field>
            @else
                <flux:field class="mb-3">
                    <flux:label>Gambar</flux:label>
                    <flux:input type="file" wire:model="image" accept="image/*" />
                    <flux:error name="image" />
                    @if($editing)
                        @php $popup = $popups->firstWhere('id', $popupId); @endphp
                        @if($popup && $popup->hasMedia('popups'))
                            <p class="text-xs text-gray-500 mt-1">Gambar saat ini:</p>
                            <img src="{{ $popup->getFirstMediaUrl('popups', 'thumb') }}" class="h-20 mt-1 rounded" alt="preview">
                        @endif
                    @endif
                </flux:field>
            @endif

            <div class="grid grid-cols-2 gap-3 mb-3">
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

            <div class="grid grid-cols-2 gap-3 mb-3">
                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.defer="is_active">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </flux:select>
                    <flux:error name="is_active" />
                </flux:field>

                <flux:field>
                    <flux:label>Urutan</flux:label>
                    <flux:input type="number" wire:model.defer="order" min="0" />
                    <flux:error name="order" />
                </flux:field>
            </div>

            <div class="flex items-center space-x-2">
                <flux:button type="submit" variant="primary">
                    {{ $editing ? 'Update' : 'Simpan' }}
                </flux:button>
                @if($editing)
                    <flux:button type="button" wire:click="resetFields">Batal</flux:button>
                @endif
            </div>
        </form>
    </div>

    <h2 class="text-xl font-semibold mt-8 mb-4 dark:text-white">Daftar Popup</h2>

    <div class="overflow-x-auto shadow-lg rounded-lg">
        <table class="table-auto w-full border-collapse text-sm text-gray-700 dark:bg-stone-50" style="text-align: left">
            <thead class="bg-gray-50 text-gray-800 uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3 border-b border-gray-300">#</th>
                    <th class="px-4 py-3 border-b border-gray-300">Preview</th>
                    <th class="px-4 py-3 border-b border-gray-300">Judul</th>
                    <th class="px-4 py-3 border-b border-gray-300">Tipe</th>
                    <th class="px-4 py-3 border-b border-gray-300">Periode</th>
                    <th class="px-4 py-3 border-b border-gray-300">Status</th>
                    <th class="px-4 py-3 border-b border-gray-300">Urutan</th>
                    <th class="px-4 py-3 border-b border-gray-300">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($popups as $key => $popup)
                    <tr class="hover:bg-gray-50 transition duration-200 bg-white">
                        <td class="px-4 py-2 border-b border-gray-200">{{ $key + 1 }}</td>
                        <td class="px-4 py-2 border-b border-gray-200">
                            @if($popup->type === 'image' && $popup->hasMedia('popups'))
                                <img src="{{ $popup->getFirstMediaUrl('popups', 'thumb') }}"
                                     alt="popup" class="h-14 w-20 object-cover rounded">
                            @elseif($popup->type === 'text')
                                <div class="h-14 w-20 bg-orange-50 flex items-center justify-center rounded text-orange-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                            @else
                                <div class="h-14 w-20 bg-gray-100 flex items-center justify-center text-gray-400 rounded text-xs">
                                    No Image
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-2 border-b border-gray-200">
                            {{ $popup->title ?? '-' }}
                            @if($popup->content)
                                <p class="text-xs text-gray-400 truncate max-w-xs">{{ Str::limit($popup->content, 50) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-2 border-b border-gray-200">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $popup->type === 'image' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $popup->type === 'image' ? 'Gambar' : 'Teks' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 border-b border-gray-200 text-xs">
                            @if($popup->start_date || $popup->end_date)
                                {{ $popup->start_date?->format('d/m/Y') ?? '∞' }}
                                –
                                {{ $popup->end_date?->format('d/m/Y') ?? '∞' }}
                            @else
                                <span class="text-gray-400">Selalu tampil</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border-b border-gray-200">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $popup->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $popup->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 border-b border-gray-200">{{ $popup->order }}</td>
                        <td class="px-4 py-2 border-b border-gray-200 space-x-1">
                            <flux:button size="sm" wire:click="editPopup({{ $popup->id }})">Edit</flux:button>
                            <flux:button
                                size="sm"
                                variant="danger"
                                x-data
                                x-on:click="if (confirm('Hapus popup ini?')) { $wire.deletePopup({{ $popup->id }}) }">
                                Hapus
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-gray-400">Belum ada popup.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
