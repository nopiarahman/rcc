<div class="max-w-6xl mx-auto p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Gambar Welcome Screen</h1>
            <p class="text-sm text-gray-500 mt-0.5">Gambar yang ditampilkan di layar pembuka aplikasi</p>
        </div>
        <flux:button variant="primary" wire:click="openForm" icon="plus">Tambah Gambar</flux:button>
    </div>

    @if(session()->has('message'))
        <div class="flex items-center gap-2 p-3 mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
        <table class="min-w-full text-sm divide-y divide-gray-100 dark:divide-neutral-700">
            <thead class="bg-gray-50 dark:bg-neutral-900">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Gambar</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Judul</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Urutan</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-neutral-700">
                @forelse($images as $image)
                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition">
                        <td class="px-4 py-3">
                            @if($image->hasMedia('welcome_images'))
                                <img src="{{ $image->getFirstMediaUrl('welcome_images', 'thumb') }}" alt="{{ $image->title }}"
                                    class="h-14 w-20 object-cover rounded-lg">
                            @else
                                <div class="h-14 w-20 bg-gray-100 dark:bg-neutral-700 rounded-lg flex items-center justify-center text-gray-400 text-xs">
                                    No Image
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $image->title }}</td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="toggleActive({{ $image->id }})" class="focus:outline-none">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium cursor-pointer transition
                                    {{ $image->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    {{ $image->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </button>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $image->order }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="edit({{ $image->id }})"
                                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    x-data x-on:click="if(confirm('Hapus gambar ini?')) $wire.delete({{ $image->id }})"
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
                        <td colspan="5" class="px-4 py-12 text-center">
                            <div class="text-gray-400 text-sm">Belum ada gambar. Klik "Tambah Gambar" untuk mulai.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Form --}}
    <flux:modal wire:model="showForm" class="max-w-lg w-full">
        <flux:heading size="lg">{{ $editing ? 'Edit Gambar' : 'Tambah Gambar Baru' }}</flux:heading>

        <form wire:submit.prevent="saveImage" class="mt-4 space-y-4">
            @if($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <flux:field>
                <flux:label>Judul <span class="text-red-500">*</span></flux:label>
                <flux:input type="text" wire:model.defer="title" placeholder="Judul gambar" />
                <flux:error name="title" />
            </flux:field>

            <flux:field>
                <flux:label>Gambar {{ $editing ? '(kosongkan jika tidak diganti)' : '' }}</flux:label>
                <flux:input type="file" wire:model="image" accept="image/*" />
                <flux:error name="image" />
                @if($editing)
                    @php $img = $images->firstWhere('id', $welcomeImageId); @endphp
                    @if($img && $img->hasMedia('welcome_images'))
                        <img src="{{ $img->getFirstMediaUrl('welcome_images', 'thumb') }}" class="h-16 mt-2 rounded-lg" alt="preview">
                    @endif
                @endif
            </flux:field>

            <div class="grid grid-cols-2 gap-3">
                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.defer="is_active">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </flux:select>
                </flux:field>
                <flux:field>
                    <flux:label>Urutan</flux:label>
                    <flux:input type="number" wire:model.defer="order" min="0" />
                    <flux:error name="order" />
                </flux:field>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button variant="ghost" wire:click="resetForm">Batal</flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $editing ? 'Perbarui' : 'Simpan' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
