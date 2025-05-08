<div class="" style="min-height: 100vh; overflow: hidden; position: relative; margin-bottom:-2rem">
        <style>
            .page-wrapper {
                display: flex;
                flex-direction: column;
                height: 100vh;
                overflow: hidden;
                position: relative;
            }
            .content-wrapper {
                flex: 1;
                overflow: hidden;
                margin-top: -2rem;
                z-index: 2;
                position: relative;
            }
            .rounded-top-xl {
                border-top-left-radius: 2rem;
                border-top-right-radius: 2rem;
            }
            .tag {
                background-color: #f4f4f4;
                border-radius: 9999px;
                padding: 4px 12px;
                font-size: 0.75rem;
                color: #333;
                margin-right: 6px;
                margin-bottom: 4px;
            }
            .size-btn {
                border-radius: 9999px;
                padding: 6px 20px;
                border: none;
                font-weight: 600;
                margin-right: 8px;
                cursor: pointer;
            }
            .size-btn.active {
                background-color: #006a3e;
                color: white;
            }
            .size-btn.inactive {
                background-color: #f2f2f2;
                color: #333;
            }
            .sticky-image {
                width: 100%;
                height: 330px;
                object-fit: cover;
                position: relative;
                z-index: 1;
                -webkit-mask-image: linear-gradient(to bottom, black 70%, transparent 100%);
                mask-image: linear-gradient(to bottom, black 75%, transparent 100%);
                animation: fadeInImage 0.8s ease-out forwards;
            }
            .content-scrollable {
                position: absolute;
                z-index: 20;
                overflow-y: auto;
                display: flex;
                flex-direction: column;
            }
            .scrollable-content {
                height: calc(100vh - 330px - 80px); /* tinggi viewport - tinggi gambar - tinggi tombol cart */
                overflow-y: auto;
                margin-top: 2rem;
                padding:0 2.5rem 0 2.5rem;
            }
            .scroll-inner {
                scrollbar-width: thin;
                scrollbar-color: #ccc transparent;
            }
            .scroll-inner::-webkit-scrollbar {
                width: 6px;
            }
            .scroll-inner::-webkit-scrollbar-thumb {
                background-color: #ccc;
                border-radius: 10px;
            }
            .header-button {
                position: fixed;
                top: 16px;
                left: 16px;
                z-index: 30;
            }
            .add-to-cart {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 25;
                margin: 0 1rem 0 1rem;
            }
            @keyframes fadeSlideUp {
                0% {
                    opacity: 0;
                    transform: translateY(60px);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            @keyframes fadeInImage {
                from {
                    opacity: 0;
                    transform: scale(1.05); /* opsional: efek zoom-in halus */
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }
            .content-scrollable {
                animation: fadeSlideUp 0.6s ease-out both;
            }
            .header-button {
                position: absolute;
                top: 20px;
                left: 0;
                right: 0;
                z-index: 10;
            }

            .header-title {
                font-size: 1.2rem;
            }
        </style>
    
    <div class="page-wrapper">
        {{-- Tombol Kembali --}}
        <div class="header-button d-flex justify-content-between align-items-center px-3 py-2">
            <a wire:navigate href="{{ route('home') }}" class="btn btn-light rounded-circle shadow-sm">
                <i class="fas fa-chevron-left"></i>
            </a>
            @if ($minuman->tag)
                <div class="bg-warning text-white px-2 py-1 rounded-3 fw-bold ms-2">
                    {{$minuman->tag}}
                </div>
            @endif
            @if (session()->has('message'))
                    <div class="alert alert-success mt-3" style="position: fixed; top:80%;border-radius:20px">
                        {{ session('message') }}
                    </div>
                @endif
        </div>
        {{-- Gambar --}}
        <img src="{{ $minuman->getFirstMediaUrl('foto') }}" alt="{{ $minuman->nama }}" class="sticky-image">
    
    
        {{-- Konten Scrollable --}}
        <div class="card rounded-top-xl content-wrapper shadow-lg" style="animation: fadeSlideUp 0.6s ease-out both;">
            <div class="scrollable-content">
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold mb-0">{{ $minuman->nama }}</h4>
                        <small class="text-muted">{{$minuman->short_description}}</small>
                    </div>
                    {{-- <div class="bg-warning text-white px-2 py-1 rounded-3 fw-bold small">★ Terfavorit</div> --}}
                    </div>
    
                    {{-- Tags --}}
                    <div class="mb-3 d-flex flex-wrap" style="text-indent: 1">
                    @foreach ($minuman->bahans as $item)
                        @if ($item->nama != 'Gelas + Straw')
                            <div class="tag">{{$item->nama}}</div>
                        @endif
                    @endforeach
                    </div>
    
                    {{-- Size --}}
                    @if ($minuman->sizes->isNotEmpty())
                    <div class="mb-3">
                        <h6 class="fw-bold">Size</h6>
                        <div class="d-flex flex-wrap">
                            @foreach ($minuman->sizes as $size)
                                <button class="size-btn {{ $minuman->default_size_id === $size->id ? 'active' : 'inactive' }}"
                                        data-price="{{ $size->price }}"
                                        data-type="size"
                                        data-id="{{ $size->id }}">
                                    {{ $size->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    {{-- Sugar --}}
                    @if ($minuman->sugars->isNotEmpty())
                    <div class="mb-3">
                        <h6 class="fw-bold">Sugar Level</h6>
                        <div class="d-flex flex-wrap">
                            @foreach ($minuman->sugars->sortBy('level') as $sugar)
                                <button class="size-btn {{ $minuman->default_sugar_id === $sugar->id ? 'active' : 'inactive' }}"
                                        data-price="{{ $sugar->price }}"
                                        data-type="sugar"
                                        data-id="{{ $sugar->id }}">
                                    {{ $sugar->level }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    {{-- Topping --}}
                    @if ($minuman->toppings->isNotEmpty())
                    <div class="mb-3">
                        <h6 class="fw-bold">Topping</h6>
                        <div class="d-flex flex-wrap">
                            @foreach ($minuman->toppings->sortByDesc('nama') as $topping)
                                <button class="size-btn {{ $minuman->default_topping_id === $topping->id ? 'active' : 'inactive' }}"
                                        data-price="{{ $topping->default_price }}"
                                        data-type="topping"
                                        data-id="{{ $topping->id }}">
                                    {{ $topping->nama }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    {{-- About --}}
                    <div class="mb-4">
                    <h6 class="fw-bold">About</h6>
                    <p class="text-muted" style="font-size: 0.9rem;">
                            {{ $minuman->deskripsi }}
                    </p>
                    </div>
                </div>

            </div>
        {{-- Add to Cart --}}
        <div class="add-to-cart p-3 d-flex justify-content-between align-items-center">
            <button wire:click="addToCart({{ $minuman->id }}, {{ $minuman->base_price }}, '{{ $minuman->default_size_id }}', '{{ $minuman->default_sugar_id }}', '{{ $minuman->default_topping_id }}')" class="btn btn-success rounded-pill px-4 py-2 fw-semibold">
                Tambah Ke Keranjang
            </button>
            <div class="fw-bold fs-5 text-dark mb-0">Rp {{ number_format($minuman->base_price, 0, ',', '.') }}</div>
        </div>
        <div>
        

        
            {{-- <h3>Keranjang Anda</h3>
            <ul>
                @foreach (session('cart', []) as $key => $item)
                    <li>Produk ID: {{ $item['id'] }} | Kuantitas: {{ $item['qty'] }} | Harga: ${{ $item['price'] }}</li>
                @endforeach
            </ul> --}}
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let basePrice = {{ $minuman->base_price }};
                let selected = {
                    size: {{ $minuman->default_size_id ?? 'null' }},
                    sugar: {{ $minuman->default_sugar_id ?? 'null' }},
                    topping: {{ $minuman->default_topping_id ?? 'null' }}
                };
                let extraPrices = {
                    size: 0,
                    sugar: 0,
                    topping: 0
                };
        
                // Set harga awal berdasarkan tombol yang aktif
                document.querySelectorAll('.size-btn.active').forEach(btn => {
                    let type = btn.dataset.type;
                    extraPrices[type] = parseInt(btn.dataset.price || 0);
                });
        
                updateTotal();
        
                // Event listener klik
                document.querySelectorAll('.size-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        let type = this.dataset.type;
        
                        // Matikan semua pilihan tipe tersebut
                        document.querySelectorAll(`.size-btn[data-type="${type}"]`).forEach(btn => {
                            btn.classList.remove('active');
                            btn.classList.add('inactive');
                        });
        
                        // Aktifkan tombol yang diklik
                        this.classList.add('active');
                        this.classList.remove('inactive');
        
                        // Update pilihan & harga tambahan
                        selected[type] = parseInt(this.dataset.id);
                        extraPrices[type] = parseInt(this.dataset.price || 0);
        
                        updateTotal();
                    });
                });
        
                function updateTotal() {
                    let total = basePrice + extraPrices.size + extraPrices.sugar + extraPrices.topping;
                    document.querySelector('.add-to-cart .fw-bold.fs-5').innerText = 'Rp ' + total.toLocaleString('id-ID');
                }
            });
        </script>
{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectedOptions = {
            size: null,
            sugar: null,
            topping: null,
        };

        // Inisialisasi default dari tombol yang sudah 'active'
        document.querySelectorAll('button[data-type].active').forEach(btn => {
            const type = btn.dataset.type;
            selectedOptions[type] = {
                id: btn.dataset.id,
                price: parseInt(btn.dataset.price || 0),
            };
        });

        // Listener klik seperti sebelumnya
        document.querySelectorAll('button[data-type]').forEach(btn => {
            btn.addEventListener('click', function () {
                const type = this.dataset.type;
                const id = this.dataset.id;
                const price = parseInt(this.dataset.price || 0);

                selectedOptions[type] = {
                    id: id,
                    price: price,
                };

                document.querySelectorAll(`button[data-type="${type}"]`).forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Tombol tambah ke keranjang
        document.querySelector('.add-to-cart button').addEventListener('click', function () {
            const drinkId = {{ $minuman->id }};
            const basePrice = {{ $minuman->base_price }};
            
            const payload = {
                drink_id: drinkId,
                base_price: basePrice,
                size_id: selectedOptions.size?.id,
                sugar_id: selectedOptions.sugar?.id,
                topping_id: selectedOptions.topping?.id,
                size_price: selectedOptions.size?.price || 0,
                sugar_price: selectedOptions.sugar?.price || 0,
            topping_price: selectedOptions.topping?.price || 0,
            };

            fetch("{{ route('cart.add') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                alert('Berhasil ditambahkan ke keranjang!');
            });
        });
    });
</script> --}}

</div>