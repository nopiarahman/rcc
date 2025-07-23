<div>
    @if ($show)
    <div 
        class="fixed inset-0 bg-black/10 z-50 flex items-center justify-center" 
        x-data 
        x-init="$watch('show', value => { if (!value) $wire.closeModal() })"
    >
        <div 
            class="bg-white p-6 rounded-lg w-full max-w-lg shadow-lg" 
            @click.away="$wire.closeModal()"
        >
            <h2 class="text-xl font-semibold mb-4">Detail HPP: {{ $makanan ? $makanan->nama : '-' }}</h2>

            <table class="w-full text-sm border mb-4">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-2">Bahan</th>
                        <th class="p-2">Harga Satuan</th>
                        <th class="p-2">Jumlah</th>
                        <th class="p-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bahanList as $bahan)
                        <tr>
                            <td class="p-2">{{ $bahan['nama'] }}</td>
                            <td class="p-2">Rp.{{ number_format($bahan['harga_satuan'], 0, ',', '.') }}</td>
                            <td class="p-2">{{ number_format($bahan['jumlah']) }} {{$bahan['satuan']}}</td>
                            <td class="p-2">Rp.{{ number_format($bahan['total'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-right font-bold">
                Total HPP: Rp{{ number_format(collect($bahanList)->sum('total'), 0, ',', '.') }}
            </div>

            <div class="mt-4 text-right">
                <flux:button wire:click="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Tutup
                </flux:button>
            </div>
        </div>
    </div>
    @endif
</div>
