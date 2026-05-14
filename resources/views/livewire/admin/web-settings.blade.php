<div class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Pengaturan Website</h1>
        <p class="text-sm text-gray-500 mt-0.5">Konfigurasi nama, tampilan, lokasi, dan operasional toko</p>
    </div>

    @if(session()->has('message'))
        <div class="flex items-center gap-2 p-3 mb-6 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">

        {{-- Identitas --}}
        <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-neutral-700">
                <h2 class="font-semibold text-gray-800 dark:text-white text-sm">Identitas Toko</h2>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Website</label>
                    <input type="text" wire:model="site_name"
                        class="w-full px-3 py-2 border border-gray-200 dark:border-neutral-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-700 dark:text-white"
                        placeholder="Nama toko / website">
                    @error('site_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tagline</label>
                    <input type="text" wire:model="tagline"
                        class="w-full px-3 py-2 border border-gray-200 dark:border-neutral-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-700 dark:text-white"
                        placeholder="Slogan singkat toko">
                    @error('tagline') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">Ditampilkan di halaman menu utama</p>
                </div>
            </div>
        </div>

        {{-- Logo & Favicon --}}
        <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-neutral-700">
                <h2 class="font-semibold text-gray-800 dark:text-white text-sm">Logo & Favicon</h2>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Logo</label>
                    @if($current_logo)
                        <img src="{{ asset('storage/' . $current_logo) }}" alt="Logo" class="h-12 w-auto mb-3 rounded">
                    @endif
                    <input type="file" wire:model="logo" accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG — maks. 2MB</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Favicon</label>
                    @if($current_favicon)
                        <img src="{{ asset('storage/' . $current_favicon) }}" alt="Favicon" class="h-12 w-12 mb-3 rounded">
                    @endif
                    <input type="file" wire:model="favicon" accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('favicon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">ICO, PNG — 32×32 atau 192×192px</p>
                </div>
            </div>
        </div>

        {{-- Gambar Halaman Botolan --}}
        <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-neutral-700">
                <h2 class="font-semibold text-gray-800 dark:text-white text-sm">Gambar Halaman Botolan</h2>
                <p class="text-xs text-gray-400 mt-0.5">Ditampilkan sebagai header sebelum pelanggan memilih minuman botolan</p>
            </div>
            <div class="p-5">
                @if($current_botolan_placeholder)
                    <img src="{{ asset('storage/' . $current_botolan_placeholder) }}"
                         alt="Botolan Placeholder"
                         class="h-32 w-full object-cover rounded-lg mb-3">
                @endif
                <input type="file" wire:model="botolan_placeholder" accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('botolan_placeholder') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @if($botolan_placeholder)
                    <img src="{{ $botolan_placeholder->temporaryUrl() }}" class="mt-2 h-24 w-full object-cover rounded-lg">
                @endif
                <p class="text-xs text-gray-400 mt-1">JPG, PNG — maks. 2MB. Gunakan foto yang menarik seperti suasana toko atau produk botolan.</p>
            </div>
        </div>

        {{-- Lokasi & WhatsApp --}}
        <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-neutral-700">
                <h2 class="font-semibold text-gray-800 dark:text-white text-sm">Lokasi & WhatsApp</h2>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Latitude</label>
                        <input type="number" step="0.00000001" wire:model="latitude"
                            class="w-full px-3 py-2 border border-gray-200 dark:border-neutral-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                            placeholder="-1.66651">
                        @error('latitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Longitude</label>
                        <input type="number" step="0.00000001" wire:model="longitude"
                            class="w-full px-3 py-2 border border-gray-200 dark:border-neutral-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                            placeholder="103.65238">
                        @error('longitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Radius Pengiriman (meter)</label>
                    <input type="number" wire:model="delivery_radius" min="100"
                        class="w-full px-3 py-2 border border-gray-200 dark:border-neutral-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                        placeholder="600">
                    @error('delivery_radius') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">Jarak maksimal pengiriman (minimal 100m)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor WhatsApp</label>
                    <input type="text" wire:model="whatsapp_number"
                        class="w-full px-3 py-2 border border-gray-200 dark:border-neutral-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                        placeholder="6281234567890">
                    @error('whatsapp_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">Tanpa tanda + di depan. Pesanan akan dikirim ke nomor ini.</p>
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                    <p class="text-xs text-blue-700 dark:text-blue-300">
                        <span class="font-semibold">Cara dapat koordinat:</span> Buka Google Maps → klik kanan lokasi toko → salin koordinat.
                    </p>
                </div>
            </div>
        </div>

        {{-- Jam Operasional --}}
        <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-neutral-700">
                <h2 class="font-semibold text-gray-800 dark:text-white text-sm">Jam Operasional</h2>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jam Buka</label>
                        <input type="time" wire:model="opening_time"
                            class="w-full px-3 py-2 border border-gray-200 dark:border-neutral-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
                        @error('opening_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jam Tutup</label>
                        <input type="time" wire:model="closing_time"
                            class="w-full px-3 py-2 border border-gray-200 dark:border-neutral-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white">
                        @error('closing_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="p-4 border border-gray-200 dark:border-neutral-600 rounded-lg space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="is_temporarily_closed" class="rounded text-red-500 w-4 h-4">
                        <div>
                            <div class="text-sm font-medium text-gray-800 dark:text-white">Tutup Sementara</div>
                            <div class="text-xs text-gray-500">Centang jika toko sedang tidak beroperasi</div>
                        </div>
                    </label>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pesan untuk Pelanggan</label>
                        <textarea wire:model="temporary_closure_message" rows="2"
                            class="w-full px-3 py-2 border border-gray-200 dark:border-neutral-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                            placeholder="Contoh: Tutup untuk renovasi, dibuka kembali 1 Juli 2025"></textarea>
                        @error('temporary_closure_message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Mode Pemesanan --}}
        <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-neutral-700">
                <h2 class="font-semibold text-gray-800 dark:text-white text-sm">Mode Pemesanan</h2>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach(['both' => ['label' => 'Delivery & Takeaway', 'icon' => '🛵🏪', 'desc' => 'Pelanggan bisa pilih keduanya'], 'delivery' => ['label' => 'Hanya Delivery', 'icon' => '🛵', 'desc' => 'Wajib antar ke alamat'], 'takeaway' => ['label' => 'Hanya Takeaway', 'icon' => '🏪', 'desc' => 'Wajib ambil sendiri']] as $value => $opt)
                    <label class="relative cursor-pointer">
                        <input type="radio" wire:model.live="order_mode" value="{{ $value }}" class="peer sr-only">
                        <div class="p-4 border-2 rounded-xl transition peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-200 dark:border-neutral-600 hover:border-gray-300">
                            <div class="text-2xl mb-1">{{ $opt['icon'] }}</div>
                            <div class="text-sm font-semibold text-gray-800 dark:text-white">{{ $opt['label'] }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $opt['desc'] }}</div>
                        </div>
                        <div class="absolute top-2 right-2 hidden peer-checked:flex items-center justify-center w-5 h-5 bg-blue-500 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Ongkos Kirim --}}
        <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-neutral-700 flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-gray-800 dark:text-white text-sm">Ongkos Kirim</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Biaya pengiriman otomatis berdasarkan jarak jalan (OSRM)</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model.live="ongkir_enabled" class="sr-only peer">
                    <div class="w-10 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-neutral-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                </label>
            </div>
            <div class="p-5 space-y-4 @if(!$ongkir_enabled) opacity-50 pointer-events-none @endif">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tarif per KM (Rp)</label>
                        <input type="number" wire:model="ongkir_per_km" min="0" step="500"
                            class="w-full px-3 py-2 border border-gray-200 dark:border-neutral-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                            placeholder="2000">
                        @error('ongkir_per_km') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-400 mt-1">Contoh: 2000 = Rp 2.000/km</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gratis Ongkir sampai (KM)</label>
                        <input type="number" wire:model="ongkir_free_km" min="0" step="0.1"
                            class="w-full px-3 py-2 border border-gray-200 dark:border-neutral-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white"
                            placeholder="0">
                        @error('ongkir_free_km') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-400 mt-1">Isi 0 jika tidak ada gratis ongkir</p>
                    </div>
                </div>
                <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-100 dark:border-amber-800">
                    <p class="text-xs text-amber-700 dark:text-amber-300">
                        <span class="font-semibold">Contoh:</span> Tarif Rp 2.000/km, gratis 1 km → jarak 3 km = Rp 4.000 ongkir.
                        Jarak dihitung berdasarkan rute jalan nyata (bukan garis lurus).
                    </p>
                </div>
            </div>
        </div>

        {{-- Tema Warna --}}
        <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-neutral-700">
                <h2 class="font-semibold text-gray-800 dark:text-white text-sm">Tema Warna</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    @foreach($themeColors as $theme)
                        <label class="relative cursor-pointer">
                            <input type="radio" id="theme_{{ $theme['name'] }}" wire:model.live="selectedTheme"
                                value="{{ $theme['name'] }}" class="peer sr-only">
                            <div class="p-3 border-2 rounded-xl transition peer-checked:border-blue-500 border-gray-200 dark:border-neutral-600 hover:border-gray-300">
                                <div class="h-10 rounded-lg mb-2" style="background: linear-gradient(to right, {{ $theme['primary_color'] }}, {{ $theme['secondary_color'] }});"></div>
                                <div class="text-xs font-medium text-center text-gray-700 dark:text-gray-300">{{ $theme['display_name'] }}</div>
                            </div>
                            <div class="absolute top-1.5 right-1.5 hidden peer-checked:flex items-center justify-center w-4 h-4 bg-blue-500 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </label>
                    @endforeach
                </div>

                @if($selectedThemeColor)
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-neutral-900 rounded-xl border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Pratinjau Warna</p>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach(['Utama' => $selectedThemeColor['primary_color'], 'Sekunder' => $selectedThemeColor['secondary_color'], 'Tombol' => $selectedThemeColor['button_bg_color'], 'Kartu' => $selectedThemeColor['card_bg_color']] as $label => $color)
                                <div>
                                    <div class="h-8 rounded-lg border border-gray-200 mb-1" style="background-color: {{ $color }}"></div>
                                    <div class="text-xs text-center text-gray-500">{{ $label }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Save Button --}}
        <div class="flex justify-end">
            <button type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
                class="flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                <span wire:loading.remove wire:target="save">Simpan Pengaturan</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Menyimpan...
                </span>
            </button>
        </div>

    </form>
</div>
