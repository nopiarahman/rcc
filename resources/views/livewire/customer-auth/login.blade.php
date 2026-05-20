<div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-6 col-lg-5 col-xl-4">
                {{-- Logo / Header --}}
                <div class="text-center mb-4">
                    @php $ws = \App\Models\WebSetting::first(); @endphp
                    @if($ws?->logo_path)
                        <img src="{{ asset('storage/' . $ws->logo_path) }}" alt="Logo" height="70" class="mb-3">
                    @else
                        <img src="{{ asset('images/logo-white.png') }}" alt="Logo" height="70" class="mb-3">
                    @endif
                    <h4 class="text-white fw-bold mb-0">{{ $ws?->site_name ?? 'Raihaan Coffee Corner' }}</h4>
                    <p class="text-white-50 small mt-1">Masuk untuk lihat histori & vouchermu</p>
                </div>

                <div class="card border-0 shadow-lg" style="border-radius:20px;background:rgba(255,255,255,0.97);">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1" style="color:#1a1a1a;">Masuk Akun</h5>
                        <p class="text-muted small mb-4">Selamat datang kembali!</p>

                        <form wire:submit="login" novalidate>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small" style="color:#374151;">Nomor HP</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0" style="border-radius:10px 0 0 10px;">
                                        <i class="fas fa-phone text-muted"></i>
                                    </span>
                                    <input type="tel" wire:model="no_hp"
                                           class="form-control border-start-0 ps-0 @error('no_hp') is-invalid @enderror"
                                           placeholder="08xx-xxxx-xxxx"
                                           style="border-radius:0 10px 10px 0;"
                                           autofocus>
                                    @error('no_hp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold small" style="color:#374151;">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0" style="border-radius:10px 0 0 10px;">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" wire:model="password"
                                           class="form-control border-start-0 ps-0"
                                           placeholder="Password kamu"
                                           style="border-radius:0 10px 10px 0;">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-semibold py-2"
                                    style="border-radius:10px;"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove>Masuk</span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm me-1"></span> Masuk...
                                </span>
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <span class="text-muted small">Belum punya akun?</span>
                            <a href="{{ route('customer.register') }}" wire:navigate class="small fw-semibold ms-1"
                               style="color:var(--button-bg-color);text-decoration:none;">Daftar Gratis</a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('home') }}" wire:navigate class="text-white-50 small">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Menu
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
