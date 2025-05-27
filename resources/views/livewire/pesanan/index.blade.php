<?php

use Livewire\Volt\Component;
use App\Models\Pesanan;
use Livewire\WithPagination;
use Carbon\Carbon;

new class extends Component
{
    use WithPagination;

    public $detailPesanan = null;
    public $pendingPage = 1;
    public $selesaiPage = 1;
    public $search = '';
    public $statusFilter = 'all';
    public $dateFilter = '';
    public $perPage = 10;
    public $selectedStatuses = [
        'menunggu_konfirmasi' => true,
        'diproses' => true,
        'diantar' => true,
        'selesai' => false,
        'dibatalkan' => false
    ];
    
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'dateFilter' => ['except' => ''],
    ];
    public function mount()
    {
        // Initialize with today's date
        $this->dateFilter = now()->format('Y-m-d');
    }
    
    public function lihatDetail($id)
    {
        try {
            \Log::info('lihatDetail called with ID:', ['id' => $id]);
            
            // Try to load with both possible relationship names
            $pesanan = Pesanan::find($id);
            \Log::info('Pesanan loaded:', ['pesanan' => $pesanan ? $pesanan->toArray() : null]);
            
            if (!$pesanan) {
                throw new \Exception('Pesanan tidak ditemukan');
            }
            
            $items = [];
            
            // Check for items in pesanan_details relationship
            if (method_exists($pesanan, 'details') && $pesanan->details->isNotEmpty()) {
                foreach ($pesanan->details as $item) {
                    $items[] = [
                        'nama' => $item->minuman?->nama ?? 'Unknown',
                        'qty' => $item->qty,
                        'price' => $item->harga,
                        'size' => $item->size?->nama ?? 'Default',
                        'sugar' => $item->sugar?->level ?? 'Default',
                        'topping' => $item->topping?->nama ?? 'Tanpa topping',
                        'catatan' => $item->catatan ?? '',
                    ];
                }
            }
            // Fallback to items relationship
            elseif (method_exists($pesanan, 'items') && $pesanan->items->isNotEmpty()) {
                foreach ($pesanan->items as $item) {
                    $items[] = [
                        'nama' => $item->minuman?->nama ?? 'Unknown',
                        'qty' => $item->qty,
                        'price' => $item->harga,
                        'size' => $item->size?->nama ?? 'Default',
                        'sugar' => $item->sugar?->level ?? 'Default',
                        'topping' => $item->topping?->nama ?? 'Tanpa topping',
                        'catatan' => $item->catatan ?? '',
                    ];
                }
            }
            // Fallback to JSON items
            elseif ($pesanan->items && is_string($pesanan->items)) {
                foreach (json_decode($pesanan->items, true) as $item) {
                    $minuman = \App\Models\Minuman::find($item['id'] ?? null);
                    $size = \App\Models\Size::find($item['size_id'] ?? null);
                    $sugar = \App\Models\Sugar::find($item['sugar_id'] ?? null);
                    $topping = \App\Models\Topping::find($item['topping_id'] ?? null);

                    $items[] = [
                        'nama' => $minuman?->nama ?? ($item['nama'] ?? 'Unknown'),
                        'qty' => $item['qty'] ?? 1,
                        'price' => $item['price'] ?? 0,
                        'size' => $size?->nama ?? ($item['size'] ?? 'Default'),
                        'sugar' => $sugar?->level ?? ($item['sugar'] ?? 'Default'),
                        'topping' => $topping?->nama ?? ($item['topping'] ?? 'Tanpa topping'),
                        'catatan' => $item['catatan'] ?? '',
                    ];
                }
            }
            
            $pesanan->parsed_items = $items;
            $this->detailPesanan = $pesanan;
            
            \Log::info('Dispatching show-order-detail event');
            // Emit event to show the modal
            $this->dispatch('show-order-detail')->self();
            \Log::info('After dispatching event');
            
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'message' => 'Gagal memuat detail pesanan: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            \Log::error('Error loading order details: ' . $e->getMessage(), [
                'order_id' => $id,
                'exception' => $e
            ]);
        }
    }
    public function getPesananBelumSelesaiProperty()
    {
        return $this->getFilteredOrders()
            ->whereIn('status', ['menunggu_konfirmasi', 'diproses', 'diantar','dikirim'])
            ->orderByRaw("FIELD(status, 'menunggu_konfirmasi', 'diproses', 'diantar','dikirim')")
            ->orderBy('created_at', 'asc')
            ->paginate($this->perPage, ['*'], 'pendingPage');
    }

    public function getPesananSelesaiProperty()
    {
        return $this->getFilteredOrders()
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->orderByRaw("FIELD(status, 'selesai', 'dibatalkan')")
            ->orderBy('updated_at', 'desc')
            ->paginate($this->perPage, ['*'], 'selesaiPage');
    }
    
    protected function getFilteredOrders()
    {
        return Pesanan::query()
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('nama_pemesan', 'like', '%' . $this->search . '%')
                      ->orWhere('nomor_pesanan', 'like', '%' . $this->search . '%')
                      ->orWhere('alamat_pengantaran', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->dateFilter, function($query) {
                $date = Carbon::parse($this->dateFilter);
                $query->whereDate('created_at', $date);
            });
    }
    public function updateStatus($pesananId, $status)
    {
        $pesanan = Pesanan::find($pesananId);
        if ($pesanan) {
            // Reset pagination untuk menghindari error tampilan
            $this->resetPage('pendingPage');
            $this->resetPage('selesaiPage');
            $oldStatus = $pesanan->status;
            $pesanan->status = $status;
            
            // Update timestamps based on status
            if ($status === 'diproses' && $oldStatus === 'menunggu_konfirmasi') {
                $pesanan->waktu_diproses = now();
            } elseif ($status === 'diantar' && $oldStatus === 'diproses') {
                $pesanan->waktu_diantar = now();
            } elseif ($status === 'selesai' && in_array($oldStatus, ['diantar', 'diproses'])) {
                $pesanan->waktu_selesai = now();
            } elseif ($status === 'dibatalkan') {
                $pesanan->waktu_dibatalkan = now();
            }
            
            $pesanan->save();
            
            // Reset detail view if it was open
            if ($this->detailPesanan && $this->detailPesanan->id == $pesananId) {
                $this->detailPesanan = $pesanan;
            }
            
            $this->dispatch('show-toast', [
                'message' => 'Status pesanan berhasil diperbarui',
                'type' => 'success'
            ]);
        }
    }
    
    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'menunggu_konfirmasi' => 'bg-yellow-100 text-yellow-800',
            'diproses' => 'bg-blue-100 text-blue-800',
            'diantar' => 'bg-indigo-100 text-indigo-800',
            'selesai' => 'bg-green-100 text-green-800',
            'dibatalkan' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
    public function getPendingCountProperty()
    {
        return Pesanan::where('status', 'menunggu_konfirmasi')->count();
    }
    
    public function getTotalPendapatanProperty()
    {
        return Pesanan::where('status', 'selesai')
            ->whereDate('created_at', $this->dateFilter ?: now())
            ->sum('total');
    }
    
    public function getTotalPesananHariIniProperty()
    {
        return Pesanan::whereDate('created_at', $this->dateFilter ?: now())
            ->count();
    }
    
    public function getPesananDiterimaProperty()
    {
        return Pesanan::where('status', 'selesai')
            ->whereDate('created_at', $this->dateFilter ?: now())
            ->count();
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingDateFilter()
    {
        $this->resetPage();
    }
    
    public function updatingSelectedStatuses()
    {
        $this->resetPage();
    }
};
?>

<div class="max-w-7xl mx-auto p-6 space-y-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Pesanan</h1>
        <div class="flex items-center space-x-4">
            <div class="relative">
                <input type="text" 
                       wire:model.live="search" 
                       placeholder="Cari pesanan..." 
                       class="pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    @if ($this->pendingCount > 0)
    <div class="mb-4 px-4 py-3 rounded-lg bg-blue-100 text-blue-800 border border-blue-200 flex items-center justify-between">
        <div>
            <strong>Pesanan Baru!</strong> Anda memiliki {{ $this->pendingCount }} pesanan menunggu konfirmasi.
        </div>
        <button wire:click="$refresh" class="text-blue-600 hover:text-blue-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
    @endif
    
    <div class="bg-white rounded-xl shadow overflow-hidden mb-8">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Ringkasan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-sm text-blue-600 font-medium">Total Pesanan Belum Selesai</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $this->pesananBelumSelesai->count() }}</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="text-sm text-yellow-600 font-medium">Menunggu Konfirmasi</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $this->pendingCount }}</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-sm text-green-600 font-medium">Total Pendapatan</div>
                    <div class="text-2xl font-bold text-gray-800">Rp {{ number_format($this->totalPendapatan, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <h2 class="text-lg font-semibold text-gray-700 mb-3 dark:text-white">Pesanan Baru & Sedang Diproses</h2>
    <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200 mb-8">
        <table class="min-w-full text-sm text-left divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Nama Pemesan</th>
                    <th class="px-4 py-3">Alamat</th>
                    <th class="px-4 py-3">Waktu</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @dd($this->pesananBelumSelesai)
                @foreach ($this->pesananBelumSelesai as $pesanan)
                
                    <tr wire:key="pesanan-belumselesai-{{ $pesanan->id }}">
                        <td class="px-4 py-2 font-medium text-gray-700">#{{ $pesanan->id }}</td>
                        <td class="px-4 py-2">{{ $pesanan->nama_pemesan }}</td>
                        <td class="px-4 py-2">{{ $pesanan->alamat_pengantaran }}</td>
                        <td class="px-4 py-2">{{ $pesanan->waktu_pengantaran }}</td>
                        <td class="px-4 py-2 text-gray-900 font-semibold">
                            Rp {{ number_format($pesanan->total, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2">
                            <select wire:change="updateStatus({{ $pesanan->id }}, $event.target.value)" class="border p-1 rounded">
                                <option value="menunggu_konfirmasi" {{ $pesanan->status == 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                <option value="diproses" {{ $pesanan->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                <option value="diantar" {{ $pesanan->status == 'diantar' ? 'selected' : '' }}>Diantar</option>
                                <option value="dikirim" {{ $pesanan->status == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                                <option value="selesai" {{ $pesanan->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ $pesanan->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </td>
                        <td class="px-4 py-2">
                            <button wire:click="lihatDetail({{ $pesanan->id }})"
                                class="text-blue-600 hover:underline text-sm">Lihat</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4 px-4">
            {{ $this->pesananBelumSelesai->links() }}
        </div>
    </div>
    <h2 class="text-lg font-semibold text-gray-700 mb-3 dark:text-white">Pesanan Selesai</h2>
    <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
        <table class="min-w-full text-sm text-left divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Nama Pemesan</th>
                    <th class="px-4 py-3">Alamat</th>
                    <th class="px-4 py-3">Waktu</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($this->pesananSelesai as $pesanan)
                    <tr wire:key="pesanan-selesai-{{ $pesanan->id }}">
                        <td class="px-4 py-2 font-medium text-gray-700">#{{ $pesanan->id }}</td>
                        <td class="px-4 py-2">{{ $pesanan->nama_pemesan }}</td>
                        <td class="px-4 py-2">{{ $pesanan->alamat_pengantaran }}</td>
                        <td class="px-4 py-2">{{ $pesanan->waktu_pengantaran }}</td>
                        <td class="px-4 py-2 text-gray-900 font-semibold">
                            Rp {{ number_format($pesanan->total, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2">
                            @if ($pesanan->status == 'dibatalkan')
                                <span class="inline-block px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">{{$pesanan->status}}</span>
                            @else
                                <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">{{$pesanan->status}}</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <button wire:click="lihatDetail({{ $pesanan->id }})"
                                class="text-blue-600 hover:underline text-sm">Lihat</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4 px-4">
            {{ $this->pesananSelesai->links() }}
        </div>
    </div>

    @if ($detailPesanan)
        <div x-data="{ open: true }" class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow mt-6 mb-8">
            <h3 class="font-bold mb-2">Detail Pesanan #{{ $detailPesanan->nomor_pesanan ?? $detailPesanan->id }}</h3>
            <div class="mb-2">
                <b>Nama Pemesan:</b> {{ $detailPesanan->nama_pemesan }}<br>
                <b>Alamat Pengantaran:</b> {{ $detailPesanan->alamat_pengantaran }}<br>
                <b>Status:</b> <span class="capitalize">{{ $detailPesanan->status }}</span><br>
                <b>Waktu Pengantaran:</b> {{ $detailPesanan->waktu_pengantaran }}<br>
                <b>Total:</b> Rp {{ number_format($detailPesanan->total, 0, ',', '.') }}
            </div>
            <div class="mb-2">
                <b>Item Pesanan:</b>
                <ul class="list-disc ml-6">
                    @foreach($detailPesanan->parsed_items ?? [] as $item)
                        <li>
                            <b>{{ $item['nama'] }}</b> - {{ $item['qty'] }} x Rp{{ number_format($item['price'],0,',','.') }}
                            <br>
                            <span class="text-xs text-gray-500">Size: {{ $item['size'] }}, Gula: {{ $item['sugar'] }}, Topping: {{ $item['topping'] }}</span>
                            @if(!empty($item['catatan']))<br><span class="text-xs text-gray-400">Catatan: {{ $item['catatan'] }}</span>@endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>
