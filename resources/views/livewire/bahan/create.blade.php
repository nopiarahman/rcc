<div class="max-w-5xl mx-auto p-6 " >
<x-navbar-minuman/>
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif
    <h2 class="text-2xl font-semibold mb-4 mt-5 dark:text-white">
        {{ $bahan_id ? 'Edit Bahan' : 'Tambah Bahan' }}
    </h2>

    {{-- FORM INPUT --}}
    <div class="max-w-sm">
        <form wire:submit="simpan" >
            <flux:field class="mb-2">
                <flux:label>Nama Bahan</flux:label>
                <flux:input type="text" wire:model.defer="nama" />
                <flux:error name="nama" />
            </flux:field>

            <flux:field class="mb-2">
                <flux:label>Satuan</flux:label>
                <flux:input type="text" wire:model.defer="satuan" />
                <flux:error name="satuan" />
            </flux:field>

            <flux:field class="mb-2">
                <flux:label>Harga Satuan</flux:label>
                <flux:input type="number" wire:model.defer="harga_satuan" />
                <flux:error name="harga_satuan" />
            </flux:field>
            <flux:field class="mb-2">
                <flux:label>Kategori</flux:label>
                <flux:select wire:model.defer="kategori">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="display">Display</option>
                    <option value="non-display">Non-display</option>
                </flux:select>
                <flux:error name="kategori" />
            </flux:field>
            
            <div class="flex items-center space-x-2">
                <flux:button type="submit" variant="primary">
                    {{ $bahan_id ? 'Update' : 'Simpan' }}
                </flux:button>
                @if ($bahan_id)
                    <flux:button wire:click="resetForm">
                        Batal
                    </flux:button>
                @endif
            </div>
        </form>
    </div>
    <h2 class="text-xl font-semibold mt-5 mb-4 dark:text-white">
        List Bahan
    </h2>
    {{-- TABEL BAHAN --}}
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
                        <td class="px-6 py-2 border-b border-gray-200">{{ $bahan->kategori }}</td>
                        <td class="px-6 py-2 border-b border-gray-200">
                            Rp{{ number_format($bahan->harga_satuan, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-2 border-b border-gray-200 space-x-2">
                            <flux:button size="sm" wire:click="edit({{ $bahan->id }})">Edit</flux:button>
                            
                            <flux:button 
                            size="sm" 
                            variant="danger" 
                            x-data 
                            x-on:click="if (confirm('Yakin ingin menghapus?')) { $wire.hapus({{ $bahan->id }}) }"
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

use App\Models\Bahan;
use Livewire\Volt\Component;

new class extends Component
{
    public string $nama = '';
    public string $satuan = '';
    public string $kategori = '';
    public $harga_satuan;
    public ?int $bahan_id = null;
    public $bahans = [];

    function mount()
    {
        $this->ambilData();
    }

    function rules()
    {
        return [
            'nama' => 'required|string|min:2',
            'kategori' => 'required|string|min:2',
            'satuan' => 'required|string',
            'harga_satuan' => 'required|numeric|min:0',
        ];
    }

    function simpan()
    {
        $this->validate();

        Bahan::updateOrCreate(
            ['id' => $this->bahan_id],
            [
                'nama' => $this->nama,
                'satuan' => $this->satuan,
                'kategori' => $this->kategori,
                'harga_satuan' => $this->harga_satuan,
            ]
        );

        $this->resetForm();
        $this->ambilData();
        session()->flash('success', 'Data berhasil disimpan!');
    }

    function edit($id)
    {
        $bahan = Bahan::findOrFail($id);
        $this->bahan_id = $bahan->id;
        $this->nama = $bahan->nama;
        $this->kategori = $bahan->kategori;
        $this->satuan = $bahan->satuan;
        $this->harga_satuan = $bahan->harga_satuan;
    }

    function hapus($id)
    {
        Bahan::destroy($id);
        $this->ambilData();
        session()->flash('success', 'Data berhasil dihapus!');
    }

    function resetForm()
    {
        $this->bahan_id = null;
        $this->nama = '';
        $this->kategori = '';
        $this->satuan = '';
        $this->harga_satuan = '';
    }

    function ambilData()
    {
        $this->bahans = Bahan::orderBy('nama')->get();
    }
};
