<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <!-- Web Settings Card -->
            <a href="{{ route('dashboard.web-settings') }}" class="group block">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-all duration-200 hover:border-blue-500 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="flex h-full flex-col justify-between">
                        <div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Web Settings</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola pengaturan website, logo, dan favicon</p>
                        </div>
                        <div class="mt-4 flex items-center text-sm font-medium text-blue-600 dark:text-blue-400">
                            Kelola
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Banners Management Card -->
            <a href="{{ route('dashboard.banners') }}" class="group block">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-all duration-200 hover:border-blue-500 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="flex h-full flex-col justify-between">
                        <div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900/50 dark:text-purple-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Kelola Banner</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola banner dan slider di halaman utama</p>
                        </div>
                        <div class="mt-4 flex items-center text-sm font-medium text-purple-600 dark:text-purple-400">
                            Kelola
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Welcome Images Management Card -->
            <a href="{{ route('admin.welcome-images') }}" class="group block">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-all duration-200 hover:border-blue-500 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="flex h-full flex-col justify-between">
                        <div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Gambar Welcome Screen</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola gambar yang ditampilkan di halaman selamat datang</p>
                        </div>
                        <div class="mt-4 flex items-center text-sm font-medium text-green-600 dark:text-green-400">
                            Kelola
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Menu Management Card -->
            <a href="{{ route('minuman.index') }}" class="group block">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-all duration-200 hover:border-blue-500 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="flex h-full flex-col justify-between">
                        <div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Kelola Menu</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola daftar menu minuman dan makanan</p>
                        </div>
                        <div class="mt-4 flex items-center text-sm font-medium text-green-600 dark:text-green-400">
                            Kelola
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Recent Activity Section -->
        <div class="relative flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Aktivitas Terbaru</h2>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Riwayat aktivitas terbaru akan muncul di sini.</p>
            <!-- Placeholder for recent activity -->
            <div class="mt-4 space-y-4">
                <div class="flex items-start">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Sistem telah diperbarui</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Versi terbaru 2.0.0 telah berhasil diinstal</p>
                        <p class="mt-1 text-xs text-gray-400">Beberapa menit yang lalu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
