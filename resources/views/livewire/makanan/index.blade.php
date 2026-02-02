<div class="max-w-4xl mx-auto p-6">
    <x-navbar-makanan/>
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
    
            <flux:button href="{{ route('makanan.create') }}" wire:navigate>
                + Tambah Makanan
            </flux:button>
        </div>
        <div class="overflow-x-auto bg-white rounded-xl shadow-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-left text-sm font-semibold text-gray-700">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">HPP</th>
<th class="px-4 py-3">Total HPP</th>
                        <th class="px-4 py-3">Harga Dasar</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($makanans as $makanan)
                        <tr>
                            <td class="px-4 py-2">{{ $makanan->nama }}</td>
                            <td class="px-4 py-2">Rp {{ number_format($makanan->hpp ?? 0,0,',','.') }}</td>
                        <td class="px-4 py-2">
                            Rp {{ number_format(
                                $makanan->bahans->sum(function($bahan) {
                                    return ($bahan->harga_satuan ?? 0) * ($bahan->pivot->jumlah ?? 0);
                                })
                            , 0, ',', '.') }}
                        </td>
                            <td class="px-4 py-2">Rp {{ number_format($makanan->base_price,0,',','.') }}</td>
                            <td class="px-4 py-2">
                                @if($makanan->is_habis)
                                    <span class="text-red-600">Habis</span>
                                @else
                                    <span class="text-green-600">Tersedia</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
    <flux:dropdown>
        <flux:button size="sm" icon:trailing="chevron-down">Aksi</flux:button>
        <flux:menu>
            <flux:menu.item
                @click="$dispatch('open-modal-foto', { url: '{{ $makanan->getFirstMediaUrl('gambar') }}' })"
            >
                Lihat Foto
            </flux:menu.item>
            <flux:menu.item 
                @click="$dispatch('open-modal-hpp-makanan', { id: {{ $makanan->id }} })"
            >
                Detail HPP
            </flux:menu.item>
            <flux:menu.item 
                href="{{ route('makanan.edit',$makanan) }}" 
                wire:navigate
            >
                Edit
            </flux:menu.item>
            @if($makanan->is_habis)
                <flux:menu.item 
                    wire:click="setStatus({{ $makanan->id }}, false)"
                    class="text-green-600"
                >
                    <i class="fa-solid fa-check mr-1"></i>Set Tersedia
                </flux:menu.item>
            @else
                <flux:menu.item 
                    wire:click="setStatus({{ $makanan->id }}, true)"
                    class="text-red-600"
                >
                    <i class="fa-solid fa-ban mr-1"></i>Set Habis
                </flux:menu.item>
            @endif
            <flux:menu.item 
                x-data 
                @click="if (confirm('Yakin ingin menghapus makanan ini?')) { $wire.hapus({{ $makanan->id }}) }"
                class="text-red-600"
            >
                <i class="fa-solid fa-trash mr-1"></i> Hapus
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4">Belum ada data makanan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@livewire('makanan.detail-hpp-modal')
<!-- Modal Lihat Foto -->
<div 
    x-data="{ show: false, url: '' }"
    x-on:open-modal-foto.window="show = true; url = $event.detail.url"
    x-show="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/10" >
    <div class="bg-white p-4 rounded shadow-lg max-w-md" @click.away="show = false">
        <h2 class="text-lg font-bold mb-2">Foto Makanan</h2>
        <img :src="url" alt="Foto Makanan" class="w-full h-auto rounded" />
        <button @click="show = false" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">Tutup</button>
    </div>
</div>
@livewire('detail-hpp-modal')
</div>
