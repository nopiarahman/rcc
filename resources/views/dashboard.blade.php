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
                        </div>
                    </div>
                </div>
            </a>

            <!-- Popup Management Card -->
            <a href="{{ route('dashboard.popups') }}" class="group block">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-all duration-200 hover:border-orange-500 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="flex h-full flex-col justify-between">
                        <div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-900/50 dark:text-orange-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Kelola Popup</h3>
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
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        {{-- Statistics Dashboard --}}
        <livewire:admin.dashboard-stats />
    </div>
</x-layouts.app>
