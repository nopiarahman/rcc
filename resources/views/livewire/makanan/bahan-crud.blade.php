<div class="max-w-5xl mx-auto p-6 ">
    <x-navbar-makanan/>
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-red-700 bg-red-100 rounded">
            {{ session('error') }}
        </div>
    @endif
    <h2 class="text-2xl font-semibold mb-4 mt-5 dark:text-white">
        {{ $bahan_id ? 'Edit Bahan Makanan' : 'Tambah Bahan Makanan' }}
    </h2>
    <div class="max-w-sm">
        <form wire:submit.prevent="simpan">
            <flux:field class="mb-2">
                <flux:label>Nama Bahan</flux:label>
                <flux:input type="text" wire:model="nama" />
                @error('nama')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </flux:field>
            <flux:field class="mb-2">
                <flux:label>Satuan</flux:label>
                <flux:input type="text" wire:model="satuan" />
                @error('satuan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </flux:field>
            <flux:field class="mb-2">
                <flux:label>Kategori</flux:label>
                <flux:select wire:model.defer="kategori">
                    @foreach($kategoriOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="kategori" />
            </flux:field>
            <flux:field class="mb-2">
                <flux:label>Harga Satuan</flux:label>
                <flux:input type="number" wire:model="harga_satuan" min="0" step="1" />
                @error('harga_satuan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </flux:field>
            <div class="flex items-center space-x-2">
                <flux:button type="submit" variant="primary">
                    {{ $bahan_id ? 'Update' : 'Simpan' }}
                </flux:button>
                @if ($bahan_id)
                    <flux:button wire:click="resetForm">Batal</flux:button>
                @endif
            </div>
        </form>
    </div>
    <h2 class="text-xl font-semibold mt-5 mb-4 dark:text-white">List Bahan Makanan</h2>
    <div class="overflow-x-auto shadow-lg rounded-lg ">
        <table class="table-auto w-full border-collapse text-base text-gray-700 dark:bg-stone-50" style="text-align: left">
            <thead class="bg-gray-50 text-gray-800 uppercase tracking-wide">
                <tr>
                    <th class="px-6 py-4 border-b border-gray-300">#</th>
                    <th class="px-6 py-4 border-b border-gray-300">Nama</th>
                    <th class="px-6 py-4 border-b border-gray-300">Satuan</th>
                    <th class="px-6 py-4 border-b border-gray-300">Kategori</th>
                    <th class="px-6 py-4 border-b border-gray-300">Harga Satuan</th>
                    <th class="px-6 py-4 border-b border-gray-300">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bahans as $i => $bahan)
                    <tr class="hover:bg-gray-50 transition duration-200 bg-white" style="line-height: 1">
                        <td class="px-6 py-2 border-b border-gray-200 ">{{ $i + 1 }}</td>
                        <td class="px-6 py-2 border-b border-gray-200">{{ $bahan->nama }}</td>
                        <td class="px-6 py-2 border-b border-gray-200">{{ $bahan->satuan }}</td>
                        <td class="px-6 py-2 border-b border-gray-200">
                            @if($bahan->kategori === \App\Models\Bahan::KATEGORI_DISPLAY)
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Display</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Non-Display</span>
                            @endif
                        </td>
                        <td class="px-6 py-2 border-b border-gray-200">Rp{{ number_format($bahan->harga_satuan, 0, ',', '.') }}</td>
                        <td class="px-6 py-2 border-b border-gray-200 space-x-2">
                            <flux:button size="sm" wire:click="edit({{ $bahan->id }})">Edit</flux:button>
                            <flux:button size="sm" variant="danger" x-data x-on:click="if (confirm('Yakin ingin menghapus?')) { $wire.hapus({{ $bahan->id }}) }">Hapus</flux:button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
