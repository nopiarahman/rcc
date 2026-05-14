<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('botolan.index') }}" wire:navigate variant="ghost" icon="arrow-left" />
        <flux:heading size="xl">Edit Botolan Minuman</flux:heading>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <div class="space-y-5">

        {{-- Pilih Minuman --}}
        <flux:field>
            <flux:label>Minuman <span class="text-red-500">*</span></flux:label>
            <select wire:model="minumanId"
                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">— Pilih minuman —</option>
                @foreach($minumans as $minuman)
                    <option value="{{ $minuman->id }}">{{ $minuman->nama }}</option>
                @endforeach
            </select>
            @error('minumanId') <flux:error>{{ $message }}</flux:error> @enderror
        </flux:field>

        {{-- Foto Botolan --}}
        <flux:field>
            <flux:label>Foto Botolan</flux:label>
            <p class="text-xs text-zinc-500 mb-1">Upload foto minuman dalam kemasan botol</p>
            @if($produk->getFirstMediaUrl('foto') && !$foto)
                <img src="{{ $produk->getFirstMediaUrl('foto') }}" class="mb-2 w-24 h-24 rounded-lg object-cover border">
            @endif
            <input type="file" wire:model="foto" accept="image/*"
                class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            @error('foto') <flux:error>{{ $message }}</flux:error> @enderror
            @if($foto)
                <img src="{{ $foto->temporaryUrl() }}" class="mt-2 w-24 h-24 rounded-lg object-cover border">
            @endif
        </flux:field>

        {{-- Ukuran & Harga --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <flux:label>Ukuran & Harga <span class="text-red-500">*</span></flux:label>
                <flux:button wire:click="addUkuran" size="sm" variant="ghost" icon="plus">Tambah Ukuran</flux:button>
            </div>
            <div class="space-y-3">
                @foreach($ukurans as $i => $ukuran)
                <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3 space-y-2">
                    <div class="flex items-center gap-2">
                        <flux:input wire:model="ukurans.{{ $i }}.label" placeholder="Contoh: 250 ml" class="flex-1" />
                        <flux:input wire:model="ukurans.{{ $i }}.harga" type="number" min="0" placeholder="Harga (Rp)" class="flex-1" />
                        @if(count($ukurans) > 1)
                            <flux:button wire:click="removeUkuran({{ $i }})" variant="ghost" icon="trash" size="sm" class="text-red-500 shrink-0" />
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-zinc-500 mb-1">Foto ukuran ini (opsional)</p>
                        @if(!empty($ukuran['existing_foto']) && empty($ukuranFotos[$i]))
                            <img src="{{ $ukuran['existing_foto'] }}" class="mb-2 w-16 h-16 rounded-lg object-cover border">
                        @endif
                        <input type="file" wire:model="ukuranFotos.{{ $i }}" accept="image/*"
                            class="block w-full text-sm text-zinc-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200">
                        @if(!empty($ukuranFotos[$i]))
                            <img src="{{ $ukuranFotos[$i]->temporaryUrl() }}" class="mt-2 w-16 h-16 rounded-lg object-cover border">
                        @endif
                        @error("ukuranFotos.$i") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @if($errors->has("ukurans.$i.label") || $errors->has("ukurans.$i.harga"))
                        <div class="text-red-500 text-xs">
                            {{ $errors->first("ukurans.$i.label") }}
                            {{ $errors->first("ukurans.$i.harga") }}
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Status --}}
        <flux:field>
            <flux:label>Tampilkan di Menu</flux:label>
            <flux:switch wire:model="is_active" />
        </flux:field>

        <div class="flex gap-3 pt-2">
            <flux:button wire:click="simpan" variant="primary">Update</flux:button>
            <flux:button href="{{ route('botolan.index') }}" wire:navigate variant="ghost">Batal</flux:button>
        </div>
    </div>
</div>
