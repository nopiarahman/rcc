<div class="max-w-4xl mx-auto p-6">
    <x-navbar-makanan/>
    @if (session()->has('success'))
    <div class="p-4 mb-4 text-green-700 bg-green-100 rounded">
        {{ session('success') }}
    </div>
@endif
<h2 class="text-xl font-bold mb-4 dark:text-white">Edit Makanan</h2>
<form wire:submit.prevent="update" class="space-y-6">
        <div>
        <flux:field class="mb-2">
    <flux:label>Foto Makanan</flux:label>
    <input type="file" wire:model="foto" class="input w-full" accept="image/*">
    @if ($foto)
        <img src="{{ $foto->temporaryUrl() }}" alt="Preview" class="mt-2 w-32 h-32 object-cover rounded">
    @elseif ($foto_url ?? false)
        <img src="{{ $foto_url }}" alt="Current" class="mt-2 w-32 h-32 object-cover rounded">
    @endif
    <flux:error name="foto" />
</flux:field>
<flux:field class="mb-2">
    <flux:label>Nama</flux:label>
    <flux:input type="text" wire:model.defer="nama" />
    <flux:error name="nama" />
</flux:field>        </div>
        <div>
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
    function loadCKEditorScript() {
        if (document.querySelector('script[src*="ckeditor5"]')) {
            initCKEditorWithRetry();
            return;
        }
        const script = document.createElement('script');
        script.src = 'https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js';
        script.onload = function() { initCKEditorWithRetry(); };
        script.onerror = function() { console.error('Failed to load CKEditor script'); };
        document.head.appendChild(script);
    }
    function initCKEditorWithRetry(attempts = 0) {
        const maxAttempts = 5;
        if (typeof ClassicEditor === 'undefined') {
            if (attempts < maxAttempts) {
                setTimeout(() => { initCKEditorWithRetry(attempts + 1); }, 200 * Math.pow(2, attempts));
            } else {
                console.error('CKEditor failed to initialize after multiple attempts');
            }
            return;
        }
        initCKEditor();
    }
    window.editorInitialized = false;
    window.editor = null;
    function initCKEditor() {
        if (window.editorInitialized) return;
        const editorElement = document.getElementById('deskripsi');
        if (!editorElement) return;
        window.editorInitialized = true;
        ClassicEditor
            .create(editorElement, {
                toolbar: [
                    'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                    'indent', 'outdent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'
                ]
            })
            .then(editor => {
                window.editor = editor;
                const initialContent = editorElement.dataset.initialValue;
                if (initialContent) { editor.setData(initialContent); }
                editor.model.document.on('change:data', () => {
                    @this.set('deskripsi', editor.getData());
                });
            })
            .catch(error => {
                console.error(error);
                window.editorInitialized = false;
            });
    }
    document.addEventListener('livewire:navigating', function() {
        if (window.editor) {
            try {
                window.editor.destroy().then(() => {
                    window.editor = null;
                    window.editorInitialized = false;
                }).catch(error => { console.error('Error during editor cleanup:', error); });
            } catch (e) {
                console.error('Error during editor cleanup:', e);
                window.editor = null;
                window.editorInitialized = false;
            }
        }
    });
    document.addEventListener('DOMContentLoaded', loadCKEditorScript);
    document.addEventListener('livewire:navigated', loadCKEditorScript);
    document.addEventListener('livewire:load', loadCKEditorScript);
</script>        </div>
        <div>
        <flux:field class="mb-2">
    <flux:label>Harga Dasar</flux:label>
    <flux:input type="number" wire:model.defer="base_price" />
    <flux:error name="base_price" />
</flux:field>        </div>
        <div>
        <flux:field class="mb-2">
    <flux:label>Kategori</flux:label>
    <flux:input type="text" wire:model.defer="kategori" />
</flux:field>        </div>
        <div>
        <div class="mb-4">
    <flux:label>Tag</flux:label>
    <select wire:model.defer="tag" class="w-full border rounded p-2">
        <option value="">Pilih default tag</option>
        <option value="Recommended">★Recommended</option>
        <option value="Terfavorit">❤︎Terfavorit</option>
        <option value="Must Try">Must Try</option>
    </select>
</div>        </div>
        <div>
        <flux:field class="mb-2">
    <flux:label>Short Description</flux:label>
    <flux:input type="text" wire:model.defer="short_description" />
    <flux:error name="short_description" />
</flux:field>        </div>
        <div>
        <div class="mb-4">
    <label class="flex items-center space-x-2">
        <input type="checkbox" wire:model.defer="is_habis" class="rounded text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <span class="text-gray-700 dark:text-gray-300">Tandai sebagai habis (out of stock)</span>
    </label>
</div>select>
        </div>
        <div>
        <div class="mb-4">
    <label class="block mb-1 font-semibold">Default Topping</label>
    <select wire:model="defaultTopping" class="w-full border rounded p-2">
        <option value="">Pilih default topping</option>
        @foreach($toppings as $topping)
            <option value="{{ $topping->id }}">{{ $topping->nama }}</option>
        @endforeach
    </select>
</div>
<div class="mb-6">
    <label class="font-semibold block mb-2">Topping</label>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($toppings as $topping)
            <div class="flex items-center gap-3 bg-gray-50 p-3 rounded border">
                <input type="checkbox" wire:model="selectedToppings.{{ $topping->id }}.aktif" class="checkbox">
                <span class="flex-1">{{ $topping->nama }}</span>
                <input type="number" wire:model="selectedToppings.{{ $topping->id }}.harga" class="input w-28" placeholder="Extra Price">
            </div>
        @endforeach
    </div>
</div>
        </div>
        <div>
        <div class="mb-6">
    <label class="font-semibold block mb-2">Bahan</label>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($bahans as $bahan)
            <div class="flex items-center gap-3 bg-gray-50 p-3 rounded border">
                <input type="checkbox" wire:model="selectedBahans.{{ $bahan->id }}.aktif" class="checkbox">
                <span class="flex-1">{{ $bahan->nama }}</span>
                <input type="number" wire:model="selectedBahans.{{ $bahan->id }}.qty" class="input w-20" placeholder="Qty">
                <input type="number" wire:model="selectedBahans.{{ $bahan->id }}.harga" class="input w-28" placeholder="Extra Price">
            </div>
        @endforeach
    </div>
</div>
        </div>
        <flux:button type="submit" variant="primary">Update</flux:button>
    </form>
</div>
