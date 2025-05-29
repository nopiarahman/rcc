<div class="max-w-4xl mx-auto p-6">
    <x-navbar-minuman/>
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-xl font-bold mb-4 dark:text-white">Tambah Minuman</h2>

    <form wire:submit.prevent="simpan" class="space-y-6">
        <flux:input type="file" wire:model="foto" label="Foto Minuman" />
        
        <flux:field class="mb-2">
            <flux:label>Nama</flux:label>
            <flux:input type="text" wire:model.defer="nama" />
            <flux:error name="nama" />
        </flux:field>

        <flux:label>Kategori</flux:label>
        <flux:select wire:model="kategori" placeholder="Pilih Kategori">
            <flux:select.option>Hot Coffee</flux:select.option>
            <flux:select.option>Iced Coffee</flux:select.option>
            <flux:select.option>Non-Coffee</flux:select.option>
            <flux:select.option>Mojito</flux:select.option>
            <flux:select.option>Matcha</flux:select.option>
        </flux:select>

        <div class="mb-4">
            <flux:label>Tag</flux:label>
            <select wire:model.defer="tag" class="w-full border rounded p-2">
                <option value="">Pilih default tag</option>
                <option value="Recommended">★Recommended</option>
                <option value="Terfavorit">❤︎Terfavorit</option>
                <option value="Must Try">Must Try</option>
            </select>
        </div>

        <flux:field class="mb-2">
            <flux:label>Short Description</flux:label>
            <flux:input type="text" wire:model.defer="short_description" />
            <flux:error name="short_description" />
        </flux:field>

        <div class="mb-4">
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <div wire:ignore>
                <textarea
                    id="deskripsi"
                    wire:model.defer="deskripsi"
                    data-initial-value="{{ $deskripsi ?? '' }}"
                    rows="8"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                >{{ $deskripsi ?? '' }}</textarea>
            </div>
            @error('deskripsi')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- CKEditor Script -->
        <script>
            // Load CKEditor script dynamically
            function loadCKEditorScript() {
                // Skip if already loaded
                if (document.querySelector('script[src*="ckeditor5"]')) {
                    initCKEditorWithRetry();
                    return;
                }
                
                // Create script element
                const script = document.createElement('script');
                script.src = 'https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js';
                script.onload = function() {
                    // Initialize editor once script is loaded
                    initCKEditorWithRetry();
                };
                script.onerror = function() {
                    console.error('Failed to load CKEditor script');
                };
                
                // Add script to document
                document.head.appendChild(script);
            }
            
            // Initialize with retry mechanism
            function initCKEditorWithRetry(attempts = 0) {
                // Maximum retry attempts
                const maxAttempts = 5;
                
                // If ClassicEditor is not defined yet, retry after a delay
                if (typeof ClassicEditor === 'undefined') {
                    if (attempts < maxAttempts) {
                        setTimeout(() => {
                            initCKEditorWithRetry(attempts + 1);
                        }, 200 * Math.pow(2, attempts)); // Exponential backoff
                    } else {
                        console.error('CKEditor failed to initialize after multiple attempts');
                    }
                    return;
                }
                
                // Now initialize the editor
                initCKEditor();
            }
            
            // Track initialization state
            window.editorInitialized = false;
            window.editor = null;
            
            function initCKEditor() {
                // Skip if already initialized
                if (window.editorInitialized) return;
                
                // Check if the editor element exists
                const editorElement = document.getElementById('deskripsi');
                if (!editorElement) return;
                
                // Set initialization flag
                window.editorInitialized = true;
                
                // Initialize CKEditor
                ClassicEditor
                    .create(document.querySelector('#deskripsi'), {
                        toolbar: [
                            'heading', '|', 
                            'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 
                            'indent', 'outdent', '|',
                            'blockQuote', 'insertTable', 'undo', 'redo'
                        ]
                    })
                    .then(editor => {
                        window.editor = editor;
                        
                        // Get initial content from data attribute
                        const initialContent = editorElement.dataset.initialValue;
                        if (initialContent) {
                            editor.setData(initialContent);
                        }
                        
                        // Update Livewire component when content changes
                        editor.model.document.on('change:data', () => {
                            @this.set('deskripsi', editor.getData());
                        });
                    })
                    .catch(error => {
                        console.error(error);
                        window.editorInitialized = false; // Reset flag on error
                    });
            }
            
            // Clean up when navigating away
            document.addEventListener('livewire:navigating', function() {
                if (window.editor) {
                    try {
                        window.editor.destroy()
                            .then(() => {
                                window.editor = null;
                                window.editorInitialized = false;
                            })
                            .catch(error => {
                                console.error('Error during editor cleanup:', error);
                            });
                    } catch (e) {
                        console.error('Error during editor cleanup:', e);
                        window.editor = null;
                        window.editorInitialized = false;
                    }
                }
            });
            
            // Initialize on various events
            document.addEventListener('DOMContentLoaded', loadCKEditorScript);
            document.addEventListener('livewire:navigated', loadCKEditorScript);
            document.addEventListener('livewire:load', loadCKEditorScript);
        </script>

        <flux:field class="mb-2">
            <flux:label>Base Price</flux:label>
            <flux:input type="number" wire:model.defer="base_price" />
            <flux:error name="base_price" />
        </flux:field>

        <div class="mb-4">
            <label class="flex items-center space-x-2">
                <input type="checkbox" wire:model.defer="is_habis" class="rounded text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <span class="text-gray-700 dark:text-gray-300">Tandai sebagai habis (out of stock)</span>
            </label>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Ukuran Default</label>
            <select wire:model="defaultSize" class="w-full border rounded p-2">
                <option value="">Pilih default size</option>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Default Sugar</label>
            <select wire:model="defaultSugar" class="w-full border rounded p-2">
                <option value="">Pilih default sugar</option>
                @foreach($sugars as $sugar)
                    <option value="{{ $sugar->id }}">{{ $sugar->level }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Default Topping</label>
            <select wire:model="defaultTopping" class="w-full border rounded p-2">
                <option value="">Pilih default topping</option>
                @foreach($toppings as $topping)
                    <option value="{{ $topping->id }}">{{ $topping->nama }}</option>
                @endforeach
            </select>
        </div>

        <hr class="mt-4 mb-4">
        {{-- BAHAN --}}
        <div class="mb-6">
            <label class="font-semibold block mb-2">Bahan</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($bahans as $bahan)
                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded border">
                        <input type="checkbox" wire:model="selectedBahans.{{ $bahan['id'] }}" class="checkbox">
                        <span class="flex-1">{{ $bahan['nama'] }}</span>
                        <input type="text" wire:model="selectedBahans.{{ $bahan['id'] }}" class="input w-32" placeholder="{{$bahan['satuan']}}">
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
    public $fotoPreview;
    public string $nama = '';
    public string $deskripsi = '';
    public string $short_description = '';
    public string $tag = '';
    public string $kategori = '';
    public int $base_price;
    public bool $is_habis = false;
    public $defaultSize;
    public $defaultSugar;
    public $defaultTopping;

    public $sizes;
    public $sugars;
    public $toppings;
    public $bahans;

    public $selectedSizes = [];
    public array $selectedSugars = [];
    public array $selectedToppings = [];
    public array $selectedBahans = [];

    public function mount()
    {
        $this->sizes = Size::all();
        $this->sugars = Sugar::all();
        $this->toppings = Topping::all();
        $this->bahans = Bahan::all();
    }

    public function simpan()
    {
        $this->validate([
            'nama' => 'required|string',
            'deskripsi' => 'nullable|string',
            'base_price' => 'required|integer|min:0',
            'defaultSize' => 'required',
            'defaultSugar' => 'required',
            'defaultTopping' => 'required',
            'tag' => 'nullable|string',
            'short_description' => 'nullable|string',
        ]);

        $minuman = Minuman::create([
            'nama' => $this->nama,
            'kategori' => $this->kategori,
            'deskripsi' => $this->deskripsi,
            'base_price' => $this->base_price,
            'is_habis' => $this->is_habis,
            'default_size_id' => $this->defaultSize,
            'default_sugar_id' => $this->defaultSugar,
            'default_topping_id' => $this->defaultTopping,
            'tag' => $this->tag,
            'short_description' => $this->short_description,
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
