<div>
    {{-- Kategori --}}
    <h5 class="fw-bold mb-2">Minuman</h5>
    <div class="d-flex gap-2 mb-3 overflow-auto">
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
        <div class="row g-3">
            @forelse($minumans as $item)
            <div class="col-6">
                    <a wire:navigate href="{{ route('minuman.detail', $item->id) }}" class="text-decoration-none text-dark">
                    <div class="card border-1 rounded-4" >
                        <img src="{{ $item->getFirstMediaUrl('foto') ?: asset('images/no-image.png') }}"
                        class="card-img-top object-fit-cover fixed-img-height rounded-4 p-2"
                        alt="{{ $item->nama }}"
                        >
                        <div class="card-body p-2">
                            <div class="fw-medium small">{{ $item->nama }}</div>
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
    </div>
</div>              