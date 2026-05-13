<div class="max-w-6xl mx-auto p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Kelola Banner</h1>
            <p class="text-sm text-gray-500 mt-0.5">Gambar yang ditampilkan di carousel halaman utama</p>
        </div>
        <flux:button variant="primary" wire:click="openForm" icon="plus">Tambah Banner</flux:button>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
        <table class="min-w-full text-sm divide-y divide-gray-100 dark:divide-neutral-700">
            <thead class="bg-gray-50 dark:bg-neutral-900">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Gambar</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Judul</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Deskripsi</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Urutan</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-neutral-700">
                @forelse($banners as $banner)
                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition">
                        <td class="px-4 py-3">
                            @if($banner->hasMedia('banners'))
                                <img src="{{ $banner->getFirstMediaUrl('banners', 'thumb') }}" alt="{{ $banner->title }}"
                                    class="h-14 w-20 object-cover rounded-lg">
                            @else
                                <div class="h-14 w-20 bg-gray-100 dark:bg-neutral-700 rounded-lg flex items-center justify-center text-gray-400 text-xs">
                                    No Image
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $banner->title }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 hidden md:table-cell max-w-xs truncate">
                            {{ $banner->description ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $banner->status ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $banner->status ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $banner->order }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="editBanner({{ $banner->id }})"
                                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    x-data x-on:click="if(confirm('Hapus banner ini?')) $wire.deleteBanner({{ $banner->id }})"
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
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="text-gray-400 text-sm">Belum ada banner. Klik "Tambah Banner" untuk mulai.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Form --}}
    <flux:modal wire:model="showForm" class="max-w-lg w-full">
        <flux:heading size="lg">{{ $editing ? 'Edit Banner' : 'Tambah Banner Baru' }}</flux:heading>

        <form wire:submit.prevent="saveBanner" class="mt-4 space-y-4">
            @if($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <flux:field>
                <flux:label>Judul <span class="text-red-500">*</span></flux:label>
                <flux:input type="text" wire:model.defer="title" placeholder="Judul banner" />
                <flux:error name="title" />
            </flux:field>

            <flux:field>
                <flux:label>Deskripsi</flux:label>
                <flux:textarea wire:model.defer="description" placeholder="Deskripsi singkat (opsional)" rows="2" />
                <flux:error name="description" />
            </flux:field>

            <flux:field>
                <flux:label>Gambar {{ $editing ? '(kosongkan jika tidak diganti)' : '' }}</flux:label>
                <flux:input type="file" wire:model="image" accept="image/*" />
                <flux:error name="image" />
                @if($editing)
                    @php $banner = $banners->firstWhere('id', $bannerId); @endphp
                    @if($banner && $banner->hasMedia('banners'))
                        <img src="{{ $banner->getFirstMediaUrl('banners', 'thumb') }}" class="h-16 mt-2 rounded-lg" alt="preview">
                    @endif
                @endif
            </flux:field>

            <div class="grid grid-cols-2 gap-3">
                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.defer="status">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </flux:select>
                    <flux:error name="status" />
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
