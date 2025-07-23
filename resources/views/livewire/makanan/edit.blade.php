<div class="max-w-4xl mx-auto p-6">
    <x-navbar-makanan/>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load CKEditor script
            if (!document.querySelector('script[src*="ckeditor5"]')) {
                const script = document.createElement('script');
                script.src = 'https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js';
                script.onload = initCKEditor;
                document.head.appendChild(script);
            } else {
                initCKEditor();
            }
        });
    
        function initCKEditor() {
            const editorElement = document.getElementById('deskripsi');
            if (!editorElement || editorElement.classList.contains('ck-editor__editable')) {
                return;
            }
    
            ClassicEditor
                .create(editorElement, {
                    toolbar: [
                        'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                        'indent', 'outdent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'
                    ]
                })
                .then(editor => {
                    // Set initial data if available
                    const initialContent = editorElement.dataset.initialValue;
                    if (initialContent) {
                        editor.setData(initialContent);
                    }
                    
                    // Update Livewire model when editor content changes
                    editor.model.document.on('change:data', () => {
                        @this.set('deskripsi', editor.getData());
                    });
                })
                .catch(error => {
                    console.error('CKEditor initialization error:', error);
                });
        }
    
        // Cleanup when navigating away
        document.addEventListener('livewire:navigating', function() {
            const editor = document.querySelector('.ck-editor__editable');
            if (editor) {
                editor.remove();
            }
        });
    </script>
    @if (session()->has('success'))
        <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Makanan</h2>
    
    <form wire:submit.prevent="update" class="space-y-6 bg-white p-6 rounded-lg shadow-md">
        <!-- Foto Makanan -->
        <div class="mb-8 p-4 bg-gray-50 rounded-lg">
            <label class="block text-sm font-medium text-gray-700 mb-3">Foto Makanan</label>
            <div class="flex flex-col sm:flex-row gap-6">
                <div class="shrink-0">
                    @if ($foto)
                        <img src="{{ $foto->temporaryUrl() }}" alt="Preview" class="h-32 w-32 object-cover rounded-lg border-2 border-dashed border-gray-300">
                    @elseif($makanan->getFirstMediaUrl('foto'))
                        <img src="{{ $makanan->getFirstMediaUrl('foto', 'preview') }}" alt="{{ $makanan->nama }}" class="h-32 w-32 object-cover rounded-lg border-2 border-dashed border-gray-300">
                    @else
                        <div class="h-32 w-32 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="relative inline-block">
                        <input type="file" wire:model="foto" id="foto-upload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <label for="foto-upload" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 cursor-pointer">
                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Pilih Gambar
                        </label>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Format: JPG, PNG, atau WebP (maks. 2MB)</p>
                    @error('foto')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Informasi Dasar -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Nama -->
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Makanan</label>
                <input type="text" id="nama" wire:model.defer="nama" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                @error('nama') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                @enderror
            </div>

            <!-- Harga Dasar -->
            <div>
                <label for="base_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Dasar</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 text-sm">Rp</span>
                    </div>
                    <input type="number" id="base_price" wire:model.defer="base_price"
                        class="block w-full pl-10 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                </div>
                @error('base_price') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                @enderror
            </div>

            <!-- Kategori -->
            <div>
                <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select id="kategori" wire:model.defer="kategori" 
                    class="mt-1 block w-full rounded-md border border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                    <option value="">Pilih Kategori</option>
                    <option value="makanan">Makanan</option>
                    <option value="snack">Snack</option>
                    <option value="makanan_berat">Makanan Berat</option>
                </select>
                @error('kategori') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                @enderror
            </div>

            <!-- Tag -->
            <div>
                <label for="tag" class="block text-sm font-medium text-gray-700 mb-1">Tag</label>
                <select id="tag" wire:model.defer="tag" 
                    class="mt-1 block w-full rounded-md border border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                    <option value="">Pilih tag</option>
                    <option value="Recommended">★ Recommended</option>
                    <option value="Terfavorit">❤︎ Terfavorit</option>
                    <option value="Must Try">Must Try</option>
                </select>
                @error('tag') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                @enderror
            </div>
        </div>
        <!-- Deskripsi Singkat -->
        <div class="mb-8">
            <label for="short_description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Singkat</label>
            <textarea 
                id="short_description" 
                wire:model.defer="short_description"
                rows="2"
                class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                placeholder="Contoh: Nasi goreng spesial dengan bumbu rahasia"
            ></textarea>
            <p class="mt-1 text-xs text-gray-500">Deskripsi singkat yang akan ditampilkan di halaman menu (maks. 160 karakter).</p>
            @error('short_description') 
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
            @enderror
        </div>
        <!-- Deskripsi -->
        <div class="mb-8">
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Lengkap</label>
            <div wire:ignore>
                <textarea
                    id="deskripsi"
                    wire:model.defer="deskripsi"
                    data-initial-value="{{ $deskripsi ?? '' }}"
                    rows="6"
                    class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                >{{ $deskripsi ?? '' }}</textarea>
            </div>
            @error('deskripsi')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>



        <!-- Status Stok -->
        {{-- <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-700">Status Ketersediaan</h3>
                    <p class="text-xs text-gray-500">Atur ketersediaan menu ini</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="is_habis" wire:model.defer="is_habis" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 transition-colors">
                        <div class="absolute top-0.5 left-0.5 bg-white w-5 h-5 rounded-full transition-transform peer-checked:translate-x-5"></div>
                    </div>
                    <span class="ml-3 text-sm font-medium text-gray-700">{{ $is_habis ? 'Habis' : 'Tersedia' }}</span>
                </label>
            </div>
        </div>  --}}
        <!-- Default Topping -->
        <div class="mb-6">
            <label class="font-semibold block mb-2">Topping</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($toppings as $topping)
                    @php
                        $isChecked = isset($selectedToppings[$topping->id]) && $selectedToppings[$topping->id]['aktif'];
                    @endphp
                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded border">
                        <input
                            type="checkbox"
                            wire:click="$toggle('selectedToppings.' . $topping->id . '.aktif')"
                            @checked($isChecked)
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        >
                        <span class="flex-1">{{ $topping->nama }}</span>
                    </div>
                @endforeach
            </div>
            @if($toppings->isEmpty())
                <p class="text-sm text-gray-500 mt-2">Tidak ada topping tersedia.</p>
            @endif
        </div>

        <!-- Bahan-bahan -->
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
                            wire:click="$toggle('selectedBahans.' . $bahan->id)"
                            @checked($isChecked)
                            class="checkbox"
                        >
                        <span class="flex-1">{{ $bahan->nama }}</span>
                        <div class="flex items-center gap-2">
                            <input
                                type="number"
                                wire:model.lazy="selectedBahans.{{ $bahan->id }}.qty"
                                class="input w-32"
                                placeholder="Qty"
                            >
                        </div>
                    </div>
                @endforeach
            </div>
            @if($bahans->isEmpty())
                <p class="text-sm text-gray-500 mt-2">Tidak ada bahan tersedia.</p>
            @endif
        </div>

        <div class="pt-4 flex gap-3">
            <flux:button type="submit" wire:loading.attr="disabled">Update</flux:button>

            <flux:button href="{{ route('makanan.index') }}">Batal</flux:button>
        </div>
    </form>
    
</div>
