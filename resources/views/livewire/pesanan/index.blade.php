<?php

use Livewire\Volt\Component;
use App\Models\Pesanan;
use Livewire\WithPagination;
use Carbon\Carbon;

new class extends Component
{
    use WithPagination;

    public $detailPesanan = null;
    public $search = '';
    public $startDate = '';
    public $endDate = '';
    public $perPage = 20;

    protected $queryString = [
        'search'    => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate'   => ['except' => ''],
    ];

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate   = now()->endOfMonth()->format('Y-m-d');
    }

    public function lihatDetail($id)
    {
        $pesanan = Pesanan::with('details')->findOrFail($id);
        $pesanan->parsed_items = $pesanan->details->map(fn($d) => [
            'nama'    => $d->nama_minuman ?? $d->nama_makanan ?? '-',
            'tipe'    => $d->nama_minuman ? 'minuman' : 'makanan',
            'qty'     => $d->qty,
            'harga'   => $d->harga,
            'subtotal'=> $d->subtotal,
            'size'    => $d->size,
            'gula'    => $d->gula,
            'topping' => $d->topping,
            'catatan' => $d->catatan,
        ])->toArray();
        $this->detailPesanan = $pesanan;
    }

    public function tutupDetail()
    {
        $this->detailPesanan = null;
    }

    public function updateStatus($pesananId, $status)
    {
        $pesanan = Pesanan::findOrFail($pesananId);
        $pesanan->status = $status;

        match($status) {
            'diproses'   => $pesanan->waktu_diproses   = now(),
            'diantar'    => $pesanan->waktu_diantar     = now(),
            'selesai'    => $pesanan->waktu_selesai     = now(),
            'dibatalkan' => $pesanan->waktu_dibatalkan  = now(),
            default      => null,
        };

        $pesanan->save();

        if ($this->detailPesanan?->id == $pesananId) {
            $this->lihatDetail($pesananId);
        }

        $this->resetPage('pendingPage');
        $this->resetPage('selesaiPage');
        $this->dispatch('toast', message: 'Status pesanan diperbarui', type: 'success');
    }

    public function selesaikanSemua()
    {
        $count = Pesanan::whereIn('status', ['menunggu_konfirmasi', 'diproses', 'diantar', 'dikirim'])->count();

        if ($count === 0) {
            $this->dispatch('toast', message: 'Tidak ada pesanan aktif', type: 'info');
            return;
        }

        Pesanan::whereIn('status', ['menunggu_konfirmasi', 'diproses', 'diantar', 'dikirim'])
            ->update(['status' => 'selesai', 'waktu_selesai' => now()]);

        $this->detailPesanan = null;
        $this->resetPage('pendingPage');
        $this->resetPage('selesaiPage');
        $this->dispatch('toast', message: "{$count} pesanan ditandai selesai", type: 'success');
    }

    protected function getFilteredOrders()
    {
        return Pesanan::query()
            ->when($this->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('nama_pemesan', 'like', "%{$this->search}%")
                       ->orWhere('nomor_pesanan', 'like', "%{$this->search}%")
                )
            )
            ->when($this->startDate && $this->endDate, fn($q) =>
                $q->whereBetween('created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay(),
                ])
            );
    }

    public function getPesananAktifProperty()
    {
        return $this->getFilteredOrders()
            ->whereIn('status', ['menunggu_konfirmasi', 'diproses', 'diantar', 'dikirim'])
            ->orderByRaw("FIELD(status, 'menunggu_konfirmasi', 'diproses', 'diantar', 'dikirim')")
            ->orderBy('created_at', 'asc')
            ->paginate($this->perPage, ['*'], 'pendingPage');
    }

    public function getPesananSelesaiProperty()
    {
        return $this->getFilteredOrders()
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->orderBy('updated_at', 'desc')
            ->paginate($this->perPage, ['*'], 'selesaiPage');
    }

    public function getAktifCountProperty()
    {
        return Pesanan::whereIn('status', ['menunggu_konfirmasi', 'diproses', 'diantar', 'dikirim'])->count();
    }

    public function getTotalPendapatanProperty()
    {
        $q = Pesanan::where('status', 'selesai');
        if ($this->startDate && $this->endDate) {
            $q->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }
        return $q->sum('total_harga');
    }

    public function setDateRange($range)
    {
        [$this->startDate, $this->endDate] = match($range) {
            'today'      => [now()->format('Y-m-d'), now()->format('Y-m-d')],
            'yesterday'  => [now()->subDay()->format('Y-m-d'), now()->subDay()->format('Y-m-d')],
            'this_week'  => [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')],
            'this_month' => [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')],
            'last_month' => [now()->subMonth()->startOfMonth()->format('Y-m-d'), now()->subMonth()->endOfMonth()->format('Y-m-d')],
            'all_time'   => ['', ''],
            default      => [$this->startDate, $this->endDate],
        };
        $this->resetPage();
    }

    public function updatingSearch()    { $this->resetPage(); }
    public function updatingStartDate() { $this->resetPage(); }
    public function updatingEndDate()   { $this->resetPage(); }
};
?>

<div class="max-w-6xl mx-auto p-4 md:p-6 space-y-5" wire:poll.30s>

    {{-- Toast --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:toast.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl text-white text-sm font-medium"
        :class="type === 'success' ? 'bg-green-600' : (type === 'info' ? 'bg-blue-500' : 'bg-red-600')"
        style="display:none">
        <span x-text="message"></span>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Daftar Pesanan</h1>
        <div class="flex items-center gap-2">
            <span wire:loading class="text-xs text-gray-400 animate-pulse">Memuat...</span>
            @if($this->aktifCount > 0)
                <button
                    wire:click="selesaikanSemua"
                    wire:confirm="Tandai semua {{ $this->aktifCount }} pesanan aktif sebagai selesai?"
                    class="flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-3 py-2 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    Selesai Semua ({{ $this->aktifCount }})
                </button>
            @endif
        </div>
    </div>

    {{-- Stats bar --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-orange-500">{{ $this->aktifCount }}</div>
            <div class="text-xs text-gray-500 mt-0.5">Pesanan Aktif</div>
        </div>
        <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-green-600">{{ $this->pesananSelesai->total() }}</div>
            <div class="text-xs text-gray-500 mt-0.5">Selesai (periode)</div>
        </div>
        <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl p-4 text-center shadow-sm">
            <div class="text-lg font-bold text-gray-800 dark:text-white">Rp {{ number_format($this->totalPendapatan, 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-0.5">Pendapatan (periode)</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl p-4 shadow-sm space-y-3">
        {{-- Quick range buttons --}}
        <div class="flex flex-wrap gap-2">
            @foreach(['today' => 'Hari Ini', 'yesterday' => 'Kemarin', 'this_week' => 'Minggu Ini', 'this_month' => 'Bulan Ini', 'last_month' => 'Bulan Lalu', 'all_time' => 'Semua'] as $key => $label)
                <button wire:click="setDateRange('{{ $key }}')"
                    class="px-3 py-1 text-xs rounded-lg font-medium transition
                        {{ ($key === 'today' && $startDate === now()->format('Y-m-d') && $endDate === now()->format('Y-m-d'))
                            ? 'bg-blue-600 text-white'
                            : 'bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-neutral-700 dark:text-gray-300' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
        {{-- Search + date --}}
        <div class="flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama / nomor pesanan..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-white">
            </div>
            <input type="date" wire:model.live="startDate"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-white">
            <input type="date" wire:model.live="endDate"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-white">
        </div>
    </div>

    {{-- ===== PESANAN AKTIF ===== --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">
            Pesanan Aktif
            @if($this->pesananAktif->total() > 0)
                <span class="ml-1 bg-orange-100 text-orange-700 text-xs px-2 py-0.5 rounded-full font-bold">{{ $this->pesananAktif->total() }}</span>
            @endif
        </h2>

        @forelse($this->pesananAktif as $pesanan)
            @php
                $statusMeta = match($pesanan->status) {
                    'menunggu_konfirmasi' => ['label' => 'Menunggu', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                    'diproses'            => ['label' => 'Diproses', 'bg' => 'bg-blue-100',   'text' => 'text-blue-800'],
                    'diantar'             => ['label' => 'Diantar',  'bg' => 'bg-indigo-100', 'text' => 'text-indigo-800'],
                    'dikirim'             => ['label' => 'Dikirim',  'bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
                    default               => ['label' => $pesanan->status, 'bg' => 'bg-gray-100', 'text' => 'text-gray-700'],
                };
            @endphp
            <div wire:key="aktif-{{ $pesanan->id }}"
                class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-sm mb-2 overflow-hidden">
                <div class="flex items-start justify-between px-4 py-3 gap-3">
                    {{-- Info utama --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ $pesanan->nama_pemesan }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $statusMeta['bg'] }} {{ $statusMeta['text'] }}">
                                {{ $statusMeta['label'] }}
                            </span>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $pesanan->order_type === 'delivery' ? 'bg-red-50 text-red-600' : 'bg-gray-100 text-gray-600' }}">
                                {{ $pesanan->order_type === 'delivery' ? '🛵 Antar' : '🏪 Ambil' }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-400 mt-1 flex flex-wrap gap-x-3 gap-y-0.5">
                            <span>{{ $pesanan->nomor_pesanan }}</span>
                            <span>{{ $pesanan->created_at->format('H:i') }} WIB</span>
                            @if($pesanan->waktu_pengantaran)
                                <span class="font-medium text-gray-600">⏰ {{ $pesanan->waktu_pengantaran }}</span>
                            @endif
                            @if($pesanan->order_type === 'delivery' && $pesanan->alamat_pengantaran !== 'Takeaway')
                                <span class="truncate max-w-xs">📍 {{ $pesanan->alamat_pengantaran }}</span>
                            @endif
                        </div>
                    </div>
                    {{-- Total --}}
                    <div class="text-right shrink-0">
                        <div class="font-bold text-gray-900 dark:text-white text-sm">
                            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                        </div>
                        <button wire:click="lihatDetail({{ $pesanan->id }})"
                            class="text-xs text-blue-600 hover:text-blue-800 mt-0.5">Detail</button>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="border-t border-gray-100 dark:border-neutral-700 px-4 py-2 flex flex-wrap gap-2 bg-gray-50 dark:bg-neutral-900">
                    @if($pesanan->status === 'menunggu_konfirmasi')
                        <button wire:click="updateStatus({{ $pesanan->id }}, 'diproses')"
                            class="flex items-center gap-1 text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition">
                            ▶ Proses
                        </button>
                    @endif
                    @if(in_array($pesanan->status, ['menunggu_konfirmasi', 'diproses']))
                        <button wire:click="updateStatus({{ $pesanan->id }}, 'diantar')"
                            class="flex items-center gap-1 text-xs font-medium bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg transition">
                            🛵 Diantar
                        </button>
                    @endif
                    <button wire:click="updateStatus({{ $pesanan->id }}, 'selesai')"
                        class="flex items-center gap-1 text-xs font-medium bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg transition">
                        ✓ Selesai
                    </button>
                    <button wire:click="updateStatus({{ $pesanan->id }}, 'dibatalkan')"
                        wire:confirm="Batalkan pesanan ini?"
                        class="flex items-center gap-1 text-xs font-medium bg-white hover:bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg transition ml-auto">
                        ✕ Batal
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-neutral-800 border border-dashed border-gray-200 dark:border-neutral-700 rounded-xl py-10 text-center text-gray-400 text-sm">
                Tidak ada pesanan aktif
            </div>
        @endforelse

        <div class="mt-2">{{ $this->pesananAktif->links() }}</div>
    </div>

    {{-- ===== PESANAN SELESAI ===== --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Riwayat Pesanan</h2>

        <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-sm overflow-hidden">
            <table class="min-w-full text-sm divide-y divide-gray-100 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-900 text-xs text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left hidden md:table-cell">No. Pesanan</th>
                        <th class="px-4 py-3 text-left hidden md:table-cell">Waktu</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-neutral-700">
                    @forelse($this->pesananSelesai as $pesanan)
                        @php
                            $isSelesai = $pesanan->status === 'selesai';
                        @endphp
                        <tr wire:key="selesai-{{ $pesanan->id }}" class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                                {{ $pesanan->nama_pemesan }}
                                <div class="text-xs text-gray-400 font-normal">
                                    {{ $pesanan->order_type === 'delivery' ? '🛵 Antar' : '🏪 Ambil' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500 hidden md:table-cell text-xs">{{ $pesanan->nomor_pesanan }}</td>
                            <td class="px-4 py-3 text-gray-500 hidden md:table-cell text-xs">
                                {{ $pesanan->created_at->format('d M, H:i') }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-white">
                                Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block px-2 py-0.5 text-xs rounded-full font-medium
                                    {{ $isSelesai ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $isSelesai ? 'Selesai' : 'Batal' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="lihatDetail({{ $pesanan->id }})"
                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">Detail</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">
                                Belum ada riwayat pesanan pada periode ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 border-t border-gray-100 dark:border-neutral-700">
                {{ $this->pesananSelesai->links() }}
            </div>
        </div>
    </div>

    {{-- ===== MODAL DETAIL ===== --}}
    @if($detailPesanan)
        @php
            $dp = $detailPesanan;
            $statusMeta = match($dp->status) {
                'menunggu_konfirmasi' => ['label' => 'Menunggu',  'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                'diproses'            => ['label' => 'Diproses',  'bg' => 'bg-blue-100',   'text' => 'text-blue-800'],
                'diantar'             => ['label' => 'Diantar',   'bg' => 'bg-indigo-100', 'text' => 'text-indigo-800'],
                'dikirim'             => ['label' => 'Dikirim',   'bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
                'selesai'             => ['label' => 'Selesai',   'bg' => 'bg-green-100',  'text' => 'text-green-800'],
                'dibatalkan'          => ['label' => 'Dibatalkan','bg' => 'bg-red-100',    'text' => 'text-red-800'],
                default               => ['label' => $dp->status, 'bg' => 'bg-gray-100',   'text' => 'text-gray-700'],
            };
        @endphp
        {{-- Backdrop --}}
        <div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" wire:click="tutupDetail"></div>

        {{-- Panel --}}
        <div class="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white dark:bg-neutral-900 shadow-2xl flex flex-col">
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-neutral-700">
                <div>
                    <div class="font-bold text-gray-900 dark:text-white text-base">{{ $dp->nomor_pesanan }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $dp->created_at->format('d M Y, H:i') }} WIB</div>
                </div>
                <button wire:click="tutupDetail" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
                {{-- Info pemesan --}}
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nama</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $dp->nama_pemesan }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Jenis</span>
                        <span class="font-medium text-gray-800 dark:text-white">
                            {{ $dp->order_type === 'delivery' ? '🛵 Antar ke Alamat' : '🏪 Ambil Sendiri' }}
                        </span>
                    </div>
                    @if($dp->order_type === 'delivery' && $dp->alamat_pengantaran !== 'Takeaway')
                        <div class="flex justify-between gap-4">
                            <span class="text-gray-500 shrink-0">Alamat</span>
                            <span class="font-medium text-gray-800 dark:text-white text-right">{{ $dp->alamat_pengantaran }}</span>
                        </div>
                    @endif
                    @if($dp->waktu_pengantaran)
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ $dp->order_type === 'delivery' ? 'Waktu Antar' : 'Waktu Ambil' }}</span>
                            <span class="font-medium text-gray-800 dark:text-white">{{ $dp->waktu_pengantaran }}</span>
                        </div>
                    @endif
                    @if($dp->catatan)
                        <div class="flex justify-between gap-4">
                            <span class="text-gray-500 shrink-0">Catatan</span>
                            <span class="text-gray-700 dark:text-gray-300 text-right italic">{{ $dp->catatan }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Status</span>
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $statusMeta['bg'] }} {{ $statusMeta['text'] }}">
                            {{ $statusMeta['label'] }}
                        </span>
                    </div>
                </div>

                {{-- Item list --}}
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Item Pesanan</h4>
                    <div class="space-y-2">
                        @foreach($dp->parsed_items ?? [] as $item)
                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg px-3 py-2.5">
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium text-gray-800 dark:text-white">{{ $item['nama'] }}</span>
                                    <span class="text-gray-600 dark:text-gray-300 font-medium">
                                        {{ $item['qty'] }}× Rp{{ number_format($item['harga'], 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-400 mt-1 flex flex-wrap gap-x-2">
                                    @if($item['tipe'] === 'minuman')
                                        @if($item['size'])   <span>Size: {{ $item['size'] }}</span> @endif
                                        @if($item['gula'])   <span>Gula: {{ $item['gula'] }}</span> @endif
                                    @endif
                                    @if($item['topping']) <span>Topping: {{ $item['topping'] }}</span> @endif
                                    @if($item['catatan']) <span class="italic">📝 {{ $item['catatan'] }}</span> @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Total --}}
                <div class="border-t border-gray-100 dark:border-neutral-700 pt-3 space-y-1.5 text-sm">
                    @if($dp->discount_amount > 0)
                        <div class="flex justify-between text-gray-500">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($dp->total_harga + $dp->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-green-600">
                            <span>Diskon</span>
                            <span>−Rp {{ number_format($dp->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-bold text-gray-900 dark:text-white text-base">
                        <span>Total</span>
                        <span>Rp {{ number_format($dp->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Footer aksi --}}
            @if(!in_array($dp->status, ['selesai', 'dibatalkan']))
                <div class="border-t border-gray-100 dark:border-neutral-700 px-5 py-3 flex flex-wrap gap-2 bg-gray-50 dark:bg-neutral-900">
                    @if($dp->status === 'menunggu_konfirmasi')
                        <button wire:click="updateStatus({{ $dp->id }}, 'diproses')"
                            class="flex-1 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg transition">
                            ▶ Proses
                        </button>
                    @endif
                    @if(in_array($dp->status, ['menunggu_konfirmasi', 'diproses']))
                        <button wire:click="updateStatus({{ $dp->id }}, 'diantar')"
                            class="flex-1 text-sm font-medium bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-lg transition">
                            🛵 Diantar
                        </button>
                    @endif
                    <button wire:click="updateStatus({{ $dp->id }}, 'selesai')"
                        class="flex-1 text-sm font-medium bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-lg transition">
                        ✓ Selesai
                    </button>
                    <button wire:click="updateStatus({{ $dp->id }}, 'dibatalkan')"
                        wire:confirm="Batalkan pesanan ini?"
                        class="text-sm font-medium text-red-600 hover:text-red-700 border border-red-200 hover:border-red-300 py-2.5 px-4 rounded-lg transition">
                        ✕
                    </button>
                </div>
            @endif
        </div>
    @endif

</div>
