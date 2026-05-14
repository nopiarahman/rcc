<div>
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">Botolan Minuman</flux:heading>
        <flux:button href="{{ route('botolan.create') }}" wire:navigate variant="primary" icon="plus">
            Tambah Botolan
        </flux:button>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-zinc-600 dark:text-zinc-300">Foto Botolan</th>
                    <th class="text-left px-4 py-3 font-semibold text-zinc-600 dark:text-zinc-300">Minuman</th>
                    <th class="text-left px-4 py-3 font-semibold text-zinc-600 dark:text-zinc-300">Ukuran & Harga</th>
                    <th class="text-left px-4 py-3 font-semibold text-zinc-600 dark:text-zinc-300">Status</th>
                    <th class="text-left px-4 py-3 font-semibold text-zinc-600 dark:text-zinc-300">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse($botolans as $botolan)
                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                    <td class="px-4 py-3">
                        @if($botolan->getFirstMediaUrl('foto'))
                            <img src="{{ $botolan->getFirstMediaUrl('foto') }}" class="w-12 h-12 rounded-lg object-cover">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center">
                                <flux:icon.photo class="text-zinc-400" />
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-zinc-900 dark:text-white">{{ $botolan->minuman?->nama ?? '—' }}</div>
                        <div class="text-xs text-zinc-400 mt-0.5">{{ $botolan->minuman?->kategori }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1">
                            @forelse($botolan->allUkurans as $ukuran)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs
                                    {{ $ukuran->is_active ? 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800' : 'bg-zinc-100 text-zinc-400 border border-zinc-200 line-through' }}">
                                    {{ $ukuran->label }} — Rp{{ number_format($ukuran->harga, 0, ',', '.') }}
                                </span>
                            @empty
                                <span class="text-zinc-400 text-xs">Belum ada ukuran</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <button wire:click="toggleActive({{ $botolan->id }})"
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium w-fit
                                {{ $botolan->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">
                            {{ $botolan->is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <flux:button href="{{ route('botolan.edit', $botolan->id) }}" wire:navigate size="sm" variant="ghost" icon="pencil" />
                            <flux:button wire:click="hapus({{ $botolan->id }})"
                                wire:confirm="Yakin ingin menghapus botolan ini? Semua ukuran akan terhapus."
                                size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-700" />
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-zinc-400">
                        Belum ada botolan. <a href="{{ route('botolan.create') }}" wire:navigate class="text-blue-500 hover:underline">Tambah sekarang</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
