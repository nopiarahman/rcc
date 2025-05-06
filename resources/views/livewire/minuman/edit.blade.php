<div class="max-w-4xl mx-auto p-6">
    <x-navbar-minuman/>

    @if (session()->has('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-xl font-bold mb-4 dark:text-white">Edit Minuman</h2>

    <form wire:submit.prevent="update" class="space-y-6">
        <flux:input type="file" wire:model="foto" label="Foto Minuman" />
        @if ($fotoPreview)
            <img src="{{ $fotoPreview }}" alt="Preview Foto" class="w-32 h-32 object-cover rounded mb-2">
        @endif
        <flux:field class="mb-2">
            <flux:label>Nama</flux:label>
            <flux:input type="text" wire:model.defer="nama" value="{{$minuman->nama}}" />
            <flux:error name="nama" />
        </flux:field>
        <flux:label>Kategori</flux:label>
        <flux:select wire:model.defer="kategori" placeholder="Pilih Kategori">
            <flux:select.option>Coffee</flux:select.option>
            <flux:select.option>Non-Coffee</flux:select.option>
            <flux:select.option>Mojito</flux:select.option>
            <flux:select.option>Matcha</flux:select.option>
        </flux:select>
        <div class="mb-4">
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea
                id="deskripsi"
                wire:model.defer="minuman.deskripsi"
                rows="4"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >{{ old('minuman.deskripsi', $minuman->deskripsi) }}</textarea>
            @error('minuman.deskripsi')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <flux:field class="mb-2">
            <flux:label>Base Price</flux:label>
            <flux:input type="number" wire:model.defer="base_price" value="{{$minuman->base_price}}" />
            <flux:error name="base_price" />
        </flux:field>

        <hr class="mt-4 mb-4">

        {{-- BAHAN --}}
        <div class="mb-6">
            <label class="font-semibold block mb-2">Bahan</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($bahans as $bahan)
                    @php
                        $isChecked = isset($selectedBahans[$bahan->id]);
                    @endphp
                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded border">
                        <input
                            type="checkbox"
                            wire:click="$toggle('selectedBahans.{{ $bahan->id }}')"
                            @checked($isChecked)
                            class="checkbox"
                        >
                        <span class="flex-1">{{ $bahan->nama }}</span>

                            <input
                                type="text"
                                wire:model="selectedBahans.{{ $bahan->id }}"
                                class="input w-32"
                                placeholder="Jumlah (ml/gr)"
                            >
                    </div>
                @endforeach
            </div>
        </div>

        {{-- SIZE --}}
        <div class="mb-6">
            <label class="font-semibold block mb-2">Ukuran</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($allSizes as $size)
                <div class="flex items-center gap-3 bg-gray-50 p-3 rounded border">
                        <input
                            type="checkbox"
                            value="{{ $size->id }}"
                            wire:model="selectedSizeIds"
                            class="checkbox"
                        />
                        <span class="flex-1">{{ $size->name }}</span>
                        <input
                            type="number"
                            wire:model.lazy="selectedSizes.{{ $size->id }}"
                            class="input input-bordered w-32 "
                            placeholder="Harga +"
                        />
                </div>
                @endforeach
            </div>
        </div>

        {{-- GULA --}}
        <div class="mb-6">
            <label class="font-semibold block mb-2">Gula</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($allSugars as $sugar)
                <div class="flex items-center gap-3 bg-gray-50 p-3 rounded border">

                        <input
                            type="checkbox"
                            value="{{ $sugar->id }}"
                            wire:model="selectedSugarIds"
                            class="checkbox"
                        />
                        <span class="flex-1">{{ $sugar->level }}</span>
                        <input
                            type="number"
                            wire:model.lazy="selectedSugars.{{ $sugar->id }}"
                            class="input w-32"
                            placeholder="Harga +"
                        />
                </div>
                @endforeach
            </div>
        </div>

        {{-- TOPPING --}}
        <div class="mb-6">
            <label class="font-semibold block mb-2">Topping</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($allToppings as $topping)
                <div class="flex items-center gap-3 bg-gray-50 p-3 rounded border">
                        <input
                            type="checkbox"
                            value="{{ $topping->id }}"
                            wire:model="selectedToppingIds"
                            class="checkbox"
                        />
                        <span class="flex-1">{{ $topping->nama }}</span>
                        <input
                            type="number"
                            wire:model.lazy="selectedToppings.{{ $topping->id }}"
                            class="input w-32"
                            placeholder="Harga +"
                        />
                </div>
                @endforeach
            </div>
        </div>


        {{-- BUTTONS --}}
        <div class="pt-4 flex gap-3">
            <flux:button type="submit" variant="primary">
                Update
            </flux:button>
            <flux:button href="{{ route('minuman.index') }}">Batal</flux:button>
        </div>

    </form>
</div>


<?php
use App\Models\Minuman;
use App\Models\Size;
use App\Models\Sugar;
use App\Models\Topping;
use App\Models\Bahan;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public $minuman;
    public $foto;
    public $fotoPreview = null;
    public $kategori;

    public $allSizes = [];
    public $allToppings = [];
    public $allSugars = [];
    public $bahans = [];

    // Bahan
    public $selectedBahans = []; // [id => jumlah]

    // Size
    public $selectedSizeIds = []; // [id, id, ...]
    public $selectedSizes = [];   // [id => extra_price]

    // Topping
    public $selectedToppingIds = [];
    public $selectedToppings = [];

    // Sugar
    public $selectedSugarIds = [];
    public $selectedSugars = [];

    public function mount(Minuman $minuman)
    {
        $this->minuman = $minuman;
        $this->kategori = $minuman->kategori;
        $this->bahans = Bahan::all();
        $this->allSizes = Size::all();
        $this->allToppings = Topping::all();
        $this->allSugars = Sugar::all();

        // Relasi Bahan: [id => jumlah]
        $this->selectedBahans = $minuman->bahans->pluck('pivot.jumlah', 'id')->toArray();

        // Relasi Size: simpan id dan harga tambahan
        $this->selectedSizeIds = $minuman->sizes->pluck('id')->toArray();
        $this->selectedSizes = $minuman->sizes->pluck('pivot.extra_price', 'id')->toArray();

        // Relasi Topping: simpan id dan harga tambahan
        $this->selectedToppingIds = $minuman->toppings->pluck('id')->toArray();
        $this->selectedToppings = $minuman->toppings->pluck('pivot.extra_price', 'id')->toArray();

        // Relasi Sugar: simpan id dan harga tambahan
        $this->selectedSugarIds = $minuman->sugars->pluck('id')->toArray();
        $this->selectedSugars = $minuman->sugars->pluck('pivot.extra_price', 'id')->toArray();

        // Foto preview
        if ($minuman->getFirstMedia('foto')) {
            $this->fotoPreview = $minuman->getFirstMediaUrl('foto');
        }
    }

    public function rules()
    {
        return [
            'minuman.nama' => 'required|string|max:255',
            'minuman.base_price' => 'required|numeric|min:0',
            'foto' => 'nullable|image|max:2048',
        ];
    }

    public function update()
    {
        $this->validate();
        $this->minuman->kategori = $this->kategori; // simpan kategori baru
        $this->minuman->save();

        // Upload foto jika ada
        if ($this->foto) {
            $this->minuman->clearMediaCollection('foto');
            $this->minuman
                ->addMedia($this->foto->getRealPath())
                ->usingFileName($this->foto->getClientOriginalName())
                ->toMediaCollection('foto');
        }

        // Sync bahan
        $syncBahans = [];
        foreach ($this->selectedBahans as $id => $jumlah) {
            if (!empty($jumlah)) {
                $syncBahans[$id] = ['jumlah' => $jumlah];
            }
        }
        $this->minuman->bahans()->sync($syncBahans);

        // Sync Size
        $sizeSyncData = [];
        foreach ($this->selectedSizeIds as $id) {
            $price = $this->selectedSizes[$id] ?? 0;
            $sizeSyncData[$id] = ['extra_price' => $price];
        }
        $this->minuman->sizes()->sync($sizeSyncData);

        // Sync Topping
        $toppingSyncData = [];
        foreach ($this->selectedToppingIds as $id) {
            $price = $this->selectedToppings[$id] ?? 0;
            $toppingSyncData[$id] = ['extra_price' => $price];
        }
        $this->minuman->toppings()->sync($toppingSyncData);

        // Sync Sugar
        $sugarSyncData = [];
        foreach ($this->selectedSugarIds as $id) {
            $price = $this->selectedSugars[$id] ?? 0;
            $sugarSyncData[$id] = ['extra_price' => $price];
        }
        $this->minuman->sugars()->sync($sugarSyncData);

        session()->flash('success', 'Data minuman berhasil diperbarui!');
        return redirect()->route('minuman.index');
    }

    public function updatedFoto($value)
    {
        if ($value) {
            $this->fotoPreview = $value->temporaryUrl();
        }
    }
};