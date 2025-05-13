<?php

use Livewire\Volt\Component;
use App\Models\Pesanan;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $detailPesanan = null;
    public $pendingPage = 1;
    public $selesaiPage = 1;
    public function lihatDetail($id)
    {
        $pesanan = Pesanan::find($id);
        $items = [];

        foreach (json_decode($pesanan->items, true) as $key => $item) {
            $minuman = \App\Models\Minuman::find($item['id']);
            $size = \App\Models\Size::find($item['size_id']);
            $sugar = \App\Models\Sugar::find($item['sugar_id']);
            $topping = \App\Models\Topping::find($item['topping_id']);

            $items[] = [
                'nama' => $minuman?->nama ?? 'Unknown',
                'qty' => $item['qty'],
                'price' => $item['price'],
                'size' => $size?->nama ?? 'Default',
                'sugar' => $sugar?->level ?? 'Default',
                'topping' => $topping?->nama ?? 'Tanpa topping',
            ];
        }

        $pesanan->parsed_items = $items;
        $this->detailPesanan = $pesanan;
    }
    public function getPesananBelumSelesaiProperty()
    {
        return Pesanan::whereIn('status', ['pending', 'diproses'])
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'pendingPage');
    }

    public function getPesananSelesaiProperty()
    {
        return Pesanan::where('status', 'selesai')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'selesaiPage');
    }
    public function getPesanansProperty()
    {
        return Pesanan::latest()->paginate(10);
    }
    public function updateStatus($pesananId, $status)
    {
        $pesanan = Pesanan::find($pesananId);
        if ($pesanan) {
            $pesanan->status = $status;
            $pesanan->save();
        }
    }
    public function getPendingCountProperty()
    {
        return Pesanan::where('status', 'pending')->count();
    }
};
?>

<div class="max-w-5xl mx-auto p-6 space-y-6">
    @if ($this->pendingCount > 0)
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-100 text-green-800 border border-green-300">
        <strong>Notifikasi:</strong> Anda memiliki pesanan baru ({{ $this->pendingCount }})
    </div>
    @endif
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
                @foreach ($this->pesananBelumSelesai as $pesanan)
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">#{{ $pesanan->id }}</td>
                        <td class="px-4 py-2">{{ $pesanan->nama_pemesan }}</td>
                        <td class="px-4 py-2">{{ $pesanan->alamat_pengantaran }}</td>
                        <td class="px-4 py-2">{{ $pesanan->waktu_pengantaran }}</td>
                        <td class="px-4 py-2 text-gray-900 font-semibold">
                            Rp {{ number_format($pesanan->total, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2">
                            <select wire:change="updateStatus({{ $pesanan->id }}, $event.target.value)" class="border p-1 rounded">
                                <option value="pending" {{ $pesanan->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="diproses" {{ $pesanan->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                <option value="selesai" {{ $pesanan->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
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
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">#{{ $pesanan->id }}</td>
                        <td class="px-4 py-2">{{ $pesanan->nama_pemesan }}</td>
                        <td class="px-4 py-2">{{ $pesanan->alamat_pengantaran }}</td>
                        <td class="px-4 py-2">{{ $pesanan->waktu_pengantaran }}</td>
                        <td class="px-4 py-2 text-gray-900 font-semibold">
                            Rp {{ number_format($pesanan->total, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2">
                            <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Selesai</span>
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
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow mt-4">
            <h3 class="font-bold mb-2">Detail Pesanan #{{ $detailPesanan->id }}</h3>
            <ul class="space-y-2">
                @foreach ($detailPesanan->parsed_items as $item)
                    <li class="border-b pb-2">
                        <div><strong>{{ $item['qty'] }}x {{ $item['nama'] }}</strong></div>
                        <div class="text-sm text-gray-600">
                            Ukuran: {{ $item['size'] }} |
                            Gula: {{ $item['sugar'] }} |
                            Topping: {{ $item['topping'] }}
                        </div>
                        <div class="text-sm font-semibold text-gray-800">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                    </li>
                @endforeach
            </ul>
    @endif
</div>