<div class="max-w-4xl mx-auto p-6">
    <x-navbar-minuman/>

    @if (session()->has('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-semibold mb-4 mt-5 dark:text-white">
        {{ $sugar_id ? 'Edit Sugar' : 'Tambah Sugar' }}
    </h2>

    {{-- FORM INPUT --}}
    <div class="max-w-sm">
        <form wire:submit="simpan">
            <flux:field class="mb-2">
                <flux:label>Level</flux:label>
                <flux:input type="text" wire:model.defer="level" />
                <flux:error name="level" />
            </flux:field>

            <flux:field class="mb-2">
                <flux:label>Price</flux:label>
                <flux:input type="number" wire:model.defer="price" />
                <flux:error name="price" />
            </flux:field>

            <div class="flex items-center space-x-2">
                <flux:button type="submit" variant="primary">
                    {{ $sugar_id ? 'Update' : 'Simpan' }}
                </flux:button>
                @if ($sugar_id)
                    <flux:button wire:click="resetForm">
                        Batal
                    </flux:button>
                @endif
            </div>
        </form>
    </div>

    <h2 class="text-xl font-semibold mt-5 mb-4 dark:text-white">
        List Sugar
    </h2>

    {{-- TABEL SUGAR --}}
    <div class="overflow-x-auto shadow-lg rounded-lg">
        <table class="table-auto w-full border-collapse text-base text-gray-700 dark:bg-stone-50" style="text-align: left">
            <thead class="bg-gray-50 text-gray-800 uppercase tracking-wide">
                <tr>
                    <th class="px-6 py-4 border-b border-gray-300">#</th>
                    <th class="px-6 py-4 border-b border-gray-300">Level</th>
                    <th class="px-6 py-4 border-b border-gray-300">Price</th>
                    <th class="px-6 py-4 border-b border-gray-300">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sugars as $i => $sugar)
                    <tr class="hover:bg-gray-50 transition duration-200 bg-white" style="line-height: 1">
                        <td class="px-6 py-2 border-b border-gray-200">{{ $i + 1 }}</td>
                        <td class="px-6 py-2 border-b border-gray-200">{{ $sugar->level }}</td>
                        <td class="px-6 py-2 border-b border-gray-200">
                            Rp{{ number_format($sugar->price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-2 border-b border-gray-200 space-x-2">
                            <flux:button size="sm" wire:click="edit({{ $sugar->id }})">Edit</flux:button>
                            <flux:button 
                                size="sm" 
                                variant="danger" 
                                x-data 
                                x-on:click="if (confirm('Yakin ingin menghapus?')) { $wire.hapus({{ $sugar->id }}) }"
                            >
                                Hapus
                            </flux:button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<?php

use App\Models\Sugar;
use Livewire\Volt\Component;

new class extends Component
{
    public string $level = '';
    public $price;
    public ?int $sugar_id = null;
    public $sugars = [];

    function mount()
    {
        $this->ambilData();
    }

    function rules()
    {
        return [
            'level' => 'required|string|min:1',
            'price' => 'required|numeric|min:0',
        ];
    }

    function simpan()
    {
        $this->validate();

        Sugar::updateOrCreate(
            ['id' => $this->sugar_id],
            [
                'level' => $this->level,
                'price' => $this->price,
            ]
        );

        $this->resetForm();
        $this->ambilData();
        session()->flash('success', 'Data sugar berhasil disimpan!');
    }

    function edit($id)
    {
        $sugar = Sugar::findOrFail($id);
        $this->sugar_id = $sugar->id;
        $this->level = $sugar->level;
        $this->price = $sugar->price;
    }

    function hapus($id)
    {
        Sugar::destroy($id);
        $this->ambilData();
        session()->flash('success', 'Data sugar berhasil dihapus!');
    }

    function resetForm()
    {
        $this->sugar_id = null;
        $this->level = '';
        $this->price = '';
    }

    function ambilData()
    {
        $this->sugars = Sugar::orderBy('level')->get();
    }
};
