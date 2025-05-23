<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Web Settings</h2>
        
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        <form wire:submit.prevent="save">
            <!-- Site Name -->
            <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                <label for="site_name" class="block text-gray-700 text-sm font-bold mb-2">
                    Nama Website
                </label>
                <input 
                    type="text" 
                    id="site_name"
                    wire:model="site_name"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Masukkan Nama Website">
                @error('site_name') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
            </div>

            <!-- Logo -->
            <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Logo Website
                </label>
                
                @if($current_logo)
                    <div class="mb-2">
                        <p class="text-sm text-gray-600 mb-2">Logo saat ini:</p>
                        <img src="{{ asset('storage/' . $current_logo) }}" alt="Current Logo" class="h-20 w-auto mb-2">
                    </div>
                @endif
                
                <div class="flex items-center">
                    <label class="w-64 flex flex-col items-center px-4 py-2 bg-white text-blue-500 rounded-lg tracking-wide uppercase border border-blue-500 cursor-pointer hover:bg-blue-500 hover:text-white">
                        <span class="text-sm leading-normal">Pilih Logo</span>
                        <input type='file' class="hidden" wire:model="logo" accept="image/*" />
                    </label>
                    <span class="ml-4 text-sm text-gray-600">
                        {{ $logo ? $logo->getClientOriginalName() : 'Belum ada file dipilih' }}
                    </span>
                </div>
                @error('logo') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                <p class="text-xs text-gray-500 mt-1">Ukuran maksimal: 2MB. Format: JPG, JPEG, PNG</p>
            </div>

            <!-- Favicon -->
            <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Favicon
                </label>
                
                @if($current_favicon)
                    <div class="mb-2">
                        <p class="text-sm text-gray-600 mb-2">Favicon saat ini:</p>
                        <img src="{{ asset('storage/' . $current_favicon) }}" alt="Current Favicon" class="h-16 w-16 mb-2">
                    </div>
                @endif
                
                <div class="flex items-center">
                    <label class="w-64 flex flex-col items-center px-4 py-2 bg-white text-blue-500 rounded-lg tracking-wide uppercase border border-blue-500 cursor-pointer hover:bg-blue-500 hover:text-white">
                        <span class="text-sm leading-normal">Pilih Favicon</span>
                        <input type='file' class="hidden" wire:model="favicon" accept="image/*" />
                    </label>
                    <span class="ml-4 text-sm text-gray-600">
                        {{ $favicon ? $favicon->getClientOriginalName() : 'Belum ada file dipilih' }}
                    </span>
                </div>
                @error('favicon') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                <p class="text-xs text-gray-500 mt-1">Ukuran: 32x32px - 192x192px. Format: ICO, PNG</p>
            </div>

            <!-- Location Settings -->
            <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                <h3 class="text-lg font-semibold mb-4">Pengaturan Lokasi Pengiriman</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="latitude" class="block text-gray-700 text-sm font-bold mb-2">
                            Latitude
                        </label>
                        <input 
                            type="number" 
                            step="0.00000001"
                            id="latitude"
                            wire:model="latitude"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="-1.66651"
                            required>
                        @error('latitude') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Contoh: -1.66651</p>
                    </div>
                    
                    <div>
                        <label for="longitude" class="block text-gray-700 text-sm font-bold mb-2">
                            Longitude
                        </label>
                        <input 
                            type="number" 
                            step="0.00000001"
                            id="longitude"
                            wire:model="longitude"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="103.65238"
                            required>
                        @error('longitude') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Contoh: 103.65238</p>
                    </div>
                </div>
                
                <div class="w-full md:w-1/2">
                    <label for="delivery_radius" class="block text-gray-700 text-sm font-bold mb-2">
                        Radius Pengiriman (meter)
                    </label>
                    <input 
                        type="number" 
                        id="delivery_radius"
                        wire:model="delivery_radius"
                        min="100"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="600"
                        required>
                    @error('delivery_radius') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-500 mt-1">Jarak maksimal pengiriman dalam meter (minimal 100m)</p>
                </div>
                
                <div class="mt-4 p-3 bg-blue-50 rounded border border-blue-100">
                    <p class="text-sm text-blue-700">
                        <span class="font-medium">Cara mendapatkan koordinat:</span> Buka <a href="https://www.google.com/maps" target="_blank" class="text-blue-600 underline">Google Maps</a>, klik kanan pada lokasi, dan salin koordinat yang muncul.
                    </p>
                </div>
            </div>

            <!-- Theme Selection -->
            <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                <label for="theme" class="block text-gray-700 text-sm font-bold mb-2">
                    Tema Warna
                </label>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach($availableThemes as $themeValue => $themeLabel)
                        @php
                            $gradient = $this->getThemeGradient($themeValue);
                            preg_match('/linear-gradient\([^,]+, *([^,]+), *([^)]+)\)/i', $gradient, $matches);
                            $color1 = $matches[1] ?? '#011a0f';
                            $color2 = $matches[2] ?? '#006a3e';
                        @endphp
                        <div class="relative">
                            <input 
                                type="radio" 
                                id="theme-{{ $themeValue }}" 
                                wire:model.live="theme"
                                wire:change="$set('selectedTheme', '{{ $themeValue }}')" 
                                value="{{ $themeValue }}" 
                                class="hidden"
                            >
                            <label 
                                for="theme-{{ $themeValue }}" 
                                class="block cursor-pointer p-4 rounded-lg border-2 transition-all duration-200 {{ $selectedTheme === $themeValue ? 'border-blue-500 ring-2 ring-blue-200 ring-offset-2 transform scale-105' : 'border-gray-200 hover:border-blue-300 hover:shadow-md' }}"
                            >
                                <div class="relative">
                                    
                                    <div class="h-16 rounded mb-2 overflow-hidden" style="height: 100px; background: {{ $gradient }}">
                                        @if($selectedTheme === $themeValue)
                                            <div class="absolute inset-0 bg-black bg-opacity-10"></div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-center font-medium text-sm mt-1">{{ $themeLabel }}</div>
                                @if($selectedTheme === $themeValue)
                                    <div class="text-center text-xs text-blue-600 font-medium mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="inline h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Dipilih
                                    </div>
                                @endif
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('theme') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end">
                <button 
                    type="submit" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    wire:loading.attr="disabled"
                    wire:target="save">
                    <span wire:loading.remove wire:target="save">Simpan Pengaturan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
