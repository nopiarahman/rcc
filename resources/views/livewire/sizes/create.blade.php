<?php

use App\Models\Size;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public $price;
    public ?int $size_id = null;
    public $sizes = [];

    function mount()
    {
        $this->ambilData();
    }

    function rules()
    {
        return [
            'name' => 'required|string|min:2',
            'price' => 'required|numeric|min:0',
        ];
    }

    function simpan()
    {
        $this->validate();

        Size::updateOrCreate(
            ['id' => $this->size_id],
            [
                'name' => $this->name,
                'price' => $this->price,
            ]
        );

        $this->resetForm();
        $this->ambilData();
        session()->flash('success', 'Size berhasil disimpan!');
    }

    function edit($id)
    {
        $size = Size::findOrFail($id);
        $this->size_id = $size->id;
        $this->name = $size->name;
        $this->price = $size->price;
    }

    function hapus($id)
    {
        Size::destroy($id);
        $this->ambilData();
        session()->flash('success', 'Size berhasil dihapus!');
    }

    function resetForm()
    {
        $this->size_id = null;
        $this->name = '';
        $this->price = '';
    }

    function ambilData()
    {
        $this->sizes = Size::orderBy('name')->get();
    }
};
?>

<div class="max-w-4xl mx-auto p-6">
    <x-navbar-minuman/>

    @if (session()->has('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-semibold mb-4 mt-5 dark:text-white">
        {{ $size_id ? 'Edit Size' : 'Tambah Size' }}
    </h2>

    <div class="max-w-sm">
        <form wire:submit="simpan">
            <flux:field class="mb-2">
                <flux:label>Nama Size</flux:label>
                <flux:input type="text" wire:model.defer="name" />
                <flux:error name="name" />
            </flux:field>

            <flux:field class="mb-2">
                <flux:label>Harga</flux:label>
                <flux:input type="number" wire:model.defer="price" />
                <flux:error name="price" />
            </flux:field>

            <div class="flex items-center space-x-2">
                <flux:button type="submit" variant="primary">
                    {{ $size_id ? 'Update' : 'Simpan' }}
                </flux:button>
                @if ($size_id)
                    <flux:button wire:click="resetForm">Batal</flux:button>
                @endif
            </div>
        </form>
    </div>

    <h2 class="text-xl font-semibold mt-5 mb-4 dark:text-white">List Size</h2>
    <div class="overflow-x-auto shadow-lg rounded-lg">
        <table class="table-auto w-full border-collapse text-base text-gray-700 dark:bg-stone-50" style="text-align: left">
            <thead class="bg-gray-100 text-gray-800 uppercase tracking-wide">
                <tr>
                    <th class="px-6 py-4 border-b">#</th>
                    <th class="px-6 py-4 border-b">Nama</th>
                    <th class="px-6 py-4 border-b">Harga</th>
                    <th class="px-6 py-4 border-b">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sizes as $i => $size)
                    <tr class="hover:bg-gray-50 transition duration-200 bg-white">
                        <td class="px-6 py-2 border-b">{{ $i + 1 }}</td>
                        <td class="px-6 py-2 border-b">{{ $size->name }}</td>
                        <td class="px-6 py-2 border-b">Rp{{ number_format($size->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-2 border-b space-x-2">
                            <flux:button size="sm" wire:click="edit({{ $size->id }})">Edit</flux:button>
                            <flux:button 
                                size="sm" 
                                variant="danger" 
                                x-data 
                                x-on:click="if (confirm('Yakin ingin menghapus?')) { $wire.hapus({{ $size->id }}) }">
                                Hapus
                            </flux:button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

