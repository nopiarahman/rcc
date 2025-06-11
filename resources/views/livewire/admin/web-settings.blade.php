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

            <!-- Tagline -->
            <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                <label for="tagline" class="block text-gray-700 text-sm font-bold mb-2">
                    Tagline Website
                </label>
                <input 
                    type="text" 
                    id="tagline"
                    wire:model="tagline"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Masukkan Tagline Website">
                @error('tagline') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                <p class="text-xs text-gray-500 mt-1">Tagline akan ditampilkan di halaman menu</p>
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
                
                <!-- WhatsApp Number -->
                <div class="mt-4 w-full">
                    <label for="whatsapp_number" class="block text-gray-700 text-sm font-bold mb-2">
                        Nomor WhatsApp
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500">+</span>
                        </div>
                        <input 
                            type="text" 
                            id="whatsapp_number"
                            wire:model="whatsapp_number"
                            class="pl-6 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="6281234567890"
                            required>
                    </div>
                    @error('whatsapp_number') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-500 mt-1">Masukkan nomor WhatsApp tanpa tanda + di depan</p>
                </div>
                
                <div class="mt-4 p-3 bg-blue-50 rounded border border-blue-100">
                    <p class="text-sm text-blue-700">
                        <span class="font-medium">Cara mendapatkan koordinat:</span> Buka <a href="https://www.google.com/maps" target="_blank" class="text-blue-600 underline">Google Maps</a>, klik kanan pada lokasi, dan salin koordinat yang muncul.
                    </p>
                </div>
            </div>

            <!-- Opening Hours -->
            <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                <h3 class="text-lg font-semibold mb-4">Jam Operasional</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="opening_time" class="block text-gray-700 text-sm font-bold mb-2">
                            Jam Buka
                        </label>
                        <input 
                            type="time" 
                            id="opening_time"
                            wire:model="opening_time"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                        @error('opening_time') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="closing_time" class="block text-gray-700 text-sm font-bold mb-2">
                            Jam Tutup
                        </label>
                        <input 
                            type="time" 
                            id="closing_time"
                            wire:model="closing_time"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                        @error('closing_time') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            wire:model="is_temporarily_closed"
                            class="form-checkbox h-5 w-5 text-blue-600">
                        <span class="ml-2 text-gray-700">Tutup Sementara</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1">Centang jika toko sedang tutup sementara</p>
                    
                        <div class="mt-3">
                            <label for="temporary_closure_message" class="block text-gray-700 text-sm font-bold mb-2">
                                Pesan Penutupan Sementara
                            </label>
                            <textarea 
                                id="temporary_closure_message"
                                wire:model="temporary_closure_message"
                                rows="3"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Contoh: Tutup sementara hingga 30 Juni 2025 untuk perbaikan"></textarea>
                            @error('temporary_closure_message') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                            <p class="text-xs text-gray-500 mt-1">Pesan ini akan ditampilkan ke pelanggan</p>
                        </div>
                </div>
            </div>

            <!-- Theme Selection -->
            <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Tema Warna
                </label>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach($themeColors as $theme)
                        <div class="relative">
                            <input type="radio" 
                                   id="theme_{{ $theme['name'] }}" 
                                   wire:model.live="selectedTheme" 
                                   value="{{ $theme['name'] }}" 
                                   class="hidden peer">
                            <label for="theme_{{ $theme['name'] }}" class="block cursor-pointer">
                                <div class="p-4 rounded-lg border-2 border-gray-200 peer-checked:border-blue-500 transition-all duration-200">
                                    <div class="w-full h-16 rounded mb-2" 
                                         style="background: linear-gradient(to right, {{ $theme['primary_color'] }}, {{ $theme['secondary_color'] }});">
                                    </div>
                                    <div class="text-center text-sm font-medium text-gray-700">
                                        {{ $theme['display_name'] }}
                                    </div>
                                    @if($selectedThemeColor && $theme['name'] === $selectedThemeColor['name'])
                                        <div class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                            Dipilih
                                        </div>
                                    @endif
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
                
                @if($selectedThemeColor)
                    <div class="mt-4 p-4 border rounded-lg bg-white">
                        <h4 class="font-bold mb-2">Pratinjau Warna</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Warna Utama</div>
                                <div class="h-8 rounded border" style="background-color: {{ $selectedThemeColor['primary_color'] }}"></div>
                                <div class="text-xs text-gray-500 mt-1">{{ $selectedThemeColor['primary_color'] }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Warna Sekunder</div>
                                <div class="h-8 rounded border" style="background-color: {{ $selectedThemeColor['secondary_color'] }}"></div>
                                <div class="text-xs text-gray-500 mt-1">{{ $selectedThemeColor['secondary_color'] }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Tombol</div>
                                <div class="h-8 rounded border flex items-center justify-center" 
                                     style="background-color: {{ $selectedThemeColor['button_bg_color'] }}; color: {{ $selectedThemeColor['button_text_color'] }};">
                                    Tombol
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Kartu</div>
                                <div class="h-8 rounded border p-2 text-sm" 
                                     style="background-color: {{ $selectedThemeColor['card_bg_color'] }}; color: {{ $selectedThemeColor['text_color'] }};">
                                    Contoh Teks
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            <!-- Order Mode Selection -->
            <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                <label class="block text-gray-700 text-sm font-bold mb-3">
                    Mode Pemesanan
                </label>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input 
                            type="radio" 
                            id="order-mode-both" 
                            wire:model.live="order_mode" 
                            value="both" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                        >
                        <label for="order-mode-both" class="ml-2 block text-sm font-medium text-gray-700">
                            Keduanya (Delivery & Takeaway)
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input 
                            type="radio" 
                            id="order-mode-delivery" 
                            wire:model.live="order_mode" 
                            value="delivery" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                        >
                        <label for="order-mode-delivery" class="ml-2 block text-sm font-medium text-gray-700">
                            Hanya Delivery
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input 
                            type="radio" 
                            id="order-mode-takeaway" 
                            wire:model.live="order_mode" 
                            value="takeaway" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                        >
                        <label for="order-mode-takeaway" class="ml-2 block text-sm font-medium text-gray-700">
                            Hanya Takeaway
                        </label>
                    </div>
                </div>
                <div class="mt-3 text-sm text-gray-600">
                </div>
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
