<div>
    {{-- Kategori --}}
    <h5 class="fw-bold mb-2">Minuman {{$filterKategori}}</h5>
    <div class="d-flex gap-2 overflow-auto">
        <button class=" btn rounded-4 btn-sm px-3 {{ $filterKategori == '' ? 'btn-success' : 'btn-outline-success' }}"
                wire:click="gantiKategori('')">Semua</button>
        @foreach($allKategoris as $kategori)
        <button wire:click="gantiKategori('{{ $kategori }}')" class=" btn rounded-4 btn-sm px-3 {{ $filterKategori == $kategori ? 'btn-success' : 'btn-outline-success' }}">
            {{ $kategori }}
        </button>
        @endforeach
    </div>
    
    {{-- Daftar Minuman --}}
    <div>
        @php
            $collections = $filterKategori == ''
                ? $minumans->groupBy('kategori')
                : collect([$filterKategori => $minumans->where('kategori', $filterKategori)]);
        @endphp
    
        @forelse ($collections as $kategori => $items)
            <div class="row g-3 mt-1">
                <h5 class="fw-bold " style="margin-bottom: -0.5rem">{{ $kategori }} Series</h5>
                @forelse ($items as $item)
                    <div class="col-6">
                        <a wire:navigate href="{{ route('minuman.detail', $item->id) }}" class="text-decoration-none text-dark">
                            <div class="card border-1 rounded-4">
                                <img src="{{ $item->getFirstMediaUrl('foto') ?: asset('images/no-image.png') }}"
                                     class="card-img-top object-fit-cover fixed-img-height rounded-4 p-2"
                                     alt="{{ $item->nama }}">
                                <div class="card-body p-2">
                                    <div class="fw-bold small" style="min-height: 2.5rem; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $item->nama }}
                                    </div>
                                    <div class="text-muted small">
                                        Rp {{ number_format(\App\Helpers\DrinkPriceHelper::calculate($item), 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <small class="text-muted">Belum ada minuman untuk kategori ini.</small>
                    </div>
                @endforelse
            </div>
        @empty
            <div class="col-12 text-center">
                <small class="text-muted">Belum ada minuman untuk kategori ini.</small>
            </div>
        @endforelse
    </div>
    
</div>              