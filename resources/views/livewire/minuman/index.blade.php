<div class="max-w-4xl mx-auto p-6">
    <x-navbar-minuman/>
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif
    <div class="max-w-5xl mx-auto ">
        <div class="flex items-center space-x-2 mb-4">
            <flux:select wire:change="onKategoriChanged($event.target.value)" class="border-gray-300 rounded-lg shadow-sm text-sm">
                <flux:select.option value="">Semua Kategori</flux:select.option>
                @foreach($allKategoris as $kategori)
                    <flux:select.option value="{{ $kategori }}">{{ $kategori }}</flux:select.option>
                @endforeach
            </flux:select>
    
            <flux:button href="{{ route('minuman.create') }}" wire:navigate>
                + Tambah Minuman
            </flux:button>
        </div>
        <div class="overflow-x-auto bg-white rounded-xl shadow-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-left text-sm font-semibold text-gray-700">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">HPP</th>
                        <th class="px-4 py-3">Harga Dasar</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-800">
                    @forelse ($minumans as $minuman)
                        <tr wire:key="minuman-{{ $minuman->id }}">
                            <td class="px-4 py-2 font-semibold">{{ $minuman->nama }}</td>
                            <td class="px-4 py-2">Rp{{ number_format($minuman->hpp, 0, ',', '.') }}</td>
                            <td class="px-4 py-2">Rp{{ number_format($minuman->base_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-2">
                                <flux:dropdown>
                                        <flux:button size="sm" icon:trailing="chevron-down">Aksi</flux:button>
                                    
                                    <flux:menu>
                                        <flux:menu.item 
                                            @click="$dispatch('open-modal-foto', { url: '{{ $minuman->getFirstMediaUrl('foto') }}' })"
                                        >
                                            Lihat Foto
                                        </flux:menu.item>
                            
                                        <flux:menu.item 
                                            @click="$dispatch('open-modal-hpp', { id: {{ $minuman->id }} })"
                                        >
                                            Detail HPP
                                        </flux:menu.item>
                            
                                        <flux:menu.item 
                                            href="{{ route('minuman.edit',$minuman) }}" 
                                            wire:navigate
                                        >
                                            Edit
                                        </flux:menu.item>
                            
                                        <flux:menu.item 
                                            x-data 
                                            @click="if (confirm('Yakin ingin menghapus minuman ini?')) { $wire.hapus({{ $minuman->id }}) }"
                                            class="text-red-600"
                                        >
                                            Hapus
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">Belum ada minuman</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal Lihat Foto -->
    <div 
        x-data="{ show: false, url: '' }"
        x-on:open-modal-foto.window="show = true; url = $event.detail.url"
        x-show="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/10" >
        <div class="bg-white p-4 rounded shadow-lg max-w-md" @click.away="show = false">
            <h2 class="text-lg font-bold mb-2">Foto Minuman</h2>
            <img :src="url" alt="Foto Minuman" class="w-full h-auto rounded" />
            <button @click="show = false" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">Tutup</button>
        </div>
    </div>
    @livewire('detail-hpp-modal')
    @livewireScripts
</div>
