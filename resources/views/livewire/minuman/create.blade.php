<div class="max-w-4xl mx-auto p-6">
    <x-navbar-minuman/>
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif
    <h2 class="text-xl font-bold mb-4 dark:text-white"">Tambah Minuman</h2>

    <form wire:submit.prevent="simpan" class="space-y-6">
        <flux:input type="file" wire:model="foto" label="Foto Minuman"/>
        <flux:field class="mb-2">
            <flux:label>Nama</flux:label>
            <flux:input type="text" wire:model.defer="nama" />
            <flux:error name="nama" />
        </flux:field>
        <flux:label>Kategori</flux:label>
        <flux:select wire:model="kategori" placeholder="Pilih Kategori">
            <flux:select.option>Coffee</flux:select.option>
            <flux:select.option>Non-Coffee</flux:select.option>
            <flux:select.option>Mojito</flux:select.option>
            <flux:select.option>Matcha</flux:select.option>
        </flux:select>

        <flux:field class="mb-2">
            <flux:textarea label="Deskripsi" wire:model.defer="deskripsi" />
            <flux:error name="deskripsi" />
        </flux:field>

        <flux:field class="mb-2">
            <flux:label>Base Price</flux:label>
            <flux:input type="number" wire:model.defer="base_price" />
            <flux:error name="base_price" />
        </flux:field>

        <hr class="mt-4 mb-4">
        {{-- BAHAN --}}
        <div class="mb-6">
            <label class="font-semibold block mb-2">Bahan</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($bahans as $bahan)
                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded border">
                        <input type="checkbox" wire:model="selectedBahans.{{ $bahan['id'] }}" class="checkbox">
                        <span class="flex-1">{{ $bahan['nama'] }}</span>
                        <input type="text" wire:model="selectedBahans.{{ $bahan['id'] }}" class="input w-32" placeholder="Jumlah (ml/gr)">
                    </div>
                @endforeach
            </div>
        </div>
        {{-- SIZE --}}
        <div class="mb-6">
            <label class="font-semibold block mb-2">Ukuran (Size)</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($sizes as $size)
                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded border">
                        <input type="checkbox" wire:model="selectedSizes.{{ $size['id'] }}.aktif" class="checkbox">
                        <span class="flex-1">{{ $size['name'] }}</span>
                        <input type="number" wire:model="selectedSizes.{{ $size['id'] }}.harga" class="input w-28" placeholder="Extra Price">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- SUGAR --}}
        <div class="mb-6">
            <label class="font-semibold block mb-2">Tingkat Gula (Sugar)</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($sugars as $sugar)
                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded border">
                        <input type="checkbox" wire:model="selectedSugars.{{ $sugar['id'] }}.aktif" class="checkbox">
                        <span class="flex-1">{{ $sugar['level'] }}</span>
                        <input type="number" wire:model="selectedSugars.{{ $sugar['id'] }}.harga" class="input w-28" placeholder="Extra Price">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- TOPPING --}}
        <div class="mb-6">
            <label class="font-semibold block mb-2">Topping</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($toppings as $topping)
                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded border">
                        <input type="checkbox" wire:model="selectedToppings.{{ $topping['id'] }}.aktif" class="checkbox">
                        <span class="flex-1">{{ $topping['nama'] }}</span>
                        <input type="number" wire:model="selectedToppings.{{ $topping['id'] }}.harga" class="input w-28" placeholder="Extra Price">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- BUTTONS --}}
        <div class="pt-4 flex gap-3">
            <flux:button type="submit" variant="primary">
                Simpan
            </flux:button>
            <flux:button href="{{ route('minuman.index') }}">Batal</flux:button>
        </div>

    </form>
</div>


<?php

use Livewire\Volt\Component;

use App\Models\Bahan;
use App\Models\Size;
use App\Models\Sugar;
use App\Models\Topping;
use App\Models\Minuman;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public $foto;

    public string $nama = '';
    public string $deskripsi = '';
    public string $kategori = '';
    public int $base_price;

    public array $sizes = [];
    public array $sugars = [];
    public array $toppings = [];
    public array $bahans = [];

    public array $selectedSizes = [];
    public array $selectedSugars = [];
    public array $selectedToppings = [];
    public array $selectedBahans = [];

    public function mount()
    {
        $this->sizes = Size::all()->toArray();
        $this->sugars = Sugar::all()->toArray();
        $this->toppings = Topping::all()->toArray();
        $this->bahans = Bahan::all()->toArray();
    }

    public function simpan()
    {
        $this->validate([
            'nama' => 'required|string',
            'deskripsi' => 'nullable|string',
            'base_price' => 'required|integer|min:0',
        ]);

        $minuman = Minuman::create([
            'nama' => $this->nama,
            'kategori' => $this->kategori,
            'deskripsi' => $this->deskripsi,
            'base_price' => $this->base_price,
        ]);
        if ($this->foto) {
            $minuman->addMedia($this->foto->getRealPath())
                ->usingFileName($this->foto->getClientOriginalName())
                ->toMediaCollection('foto');
        }
        // Sinkronisasi relasi
        $minuman->sizes()->sync(
            collect($this->selectedSizes)
                ->filter(fn($data) => $data['aktif'] ?? false)
                ->mapWithKeys(fn($data, $id) => [$id => ['extra_price' => $data['harga'] ?? 0]])
        );

        $minuman->sugars()->sync(
            collect($this->selectedSugars)
                ->filter(fn($data) => $data['aktif'] ?? false)
                ->mapWithKeys(fn($data, $id) => [$id => ['extra_price' => $data['harga'] ?? 0]])
        );

        $minuman->toppings()->sync(
            collect($this->selectedToppings)
                ->filter(fn($data) => $data['aktif'] ?? false)
                ->mapWithKeys(fn($data, $id) => [$id => ['extra_price' => $data['harga'] ?? 0]])
        );

        $minuman->bahans()->sync(
            collect($this->selectedBahans)->mapWithKeys(fn($jumlah, $id) => [$id => ['jumlah' => $jumlah]])
        );

        session()->flash('success', 'Minuman berhasil disimpan!');
        return redirect()->route('minuman.index');
    }

    // public function render()
    // {
    //     return view('livewire.minuman-create');
    // }
};
