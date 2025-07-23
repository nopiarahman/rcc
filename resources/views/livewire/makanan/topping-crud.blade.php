<div class="max-w-5xl mx-auto p-6 ">
    <x-navbar-makanan/>
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif
    <h2 class="text-2xl font-semibold mb-4 mt-5 dark:text-white">
        {{ $topping_id ? 'Edit Topping Makanan' : 'Tambah Topping Makanan' }}
    </h2>
    <div class="max-w-sm">
        <form wire:submit.prevent="simpan">
            <flux:field class="mb-2">
                <flux:label>Nama Topping</flux:label>
                <flux:input type="text" wire:model.defer="nama" />
                <flux:error name="nama" />
            </flux:field>
            <flux:field class="mb-2">
                <flux:label>Harga Default</flux:label>
                <flux:input type="number" wire:model.defer="default_price" />
                <flux:error name="default_price" />
            </flux:field>
            <div class="flex items-center space-x-2">
                <flux:button type="submit" variant="primary">
                    {{ $topping_id ? 'Update' : 'Simpan' }}
                </flux:button>
                @if ($topping_id)
                    <flux:button wire:click="resetForm">Batal</flux:button>
                @endif
            </div>
        </form>
    </div>
    <h2 class="text-xl font-semibold mt-5 mb-4 dark:text-white">List Topping Makanan</h2>
    <div class="overflow-x-auto shadow-lg rounded-lg ">
        <table class="table-auto w-full border-collapse text-base text-gray-700 dark:bg-stone-50" style="text-align: left">
            <thead class="bg-gray-50 text-gray-800 uppercase tracking-wide">
                <tr>
                    <th class="px-6 py-4 border-b border-gray-300">#</th>
                    <th class="px-6 py-4 border-b border-gray-300">Nama</th>
                    <th class="px-6 py-4 border-b border-gray-300">Harga Default</th>
                    <th class="px-6 py-4 border-b border-gray-300">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($toppings as $i => $topping)
                    <tr class="hover:bg-gray-50 transition duration-200 bg-white" style="line-height: 1">
                        <td class="px-6 py-2 border-b border-gray-200 ">{{ $i + 1 }}</td>
                        <td class="px-6 py-2 border-b border-gray-200">{{ $topping->nama }}</td>
                        <td class="px-6 py-2 border-b border-gray-200">Rp{{ number_format($topping->default_price, 0, ',', '.') }}</td>
                        <td class="px-6 py-2 border-b border-gray-200 space-x-2">
                            <flux:button size="sm" wire:click="edit({{ $topping->id }})">Edit</flux:button>
                            <flux:button size="sm" variant="danger" x-data x-on:click="if (confirm('Yakin ingin menghapus?')) { $wire.hapus({{ $topping->id }}) }">Hapus</flux:button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
