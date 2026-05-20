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
                    <p class="text-white-50 small mt-1">Buat akun untuk mulai menikmati promo</p>
                </div>

                {{-- Card --}}
                <div class="card border-0 shadow-lg" style="border-radius:20px;backdrop-filter:blur(10px);background:rgba(255,255,255,0.97);">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1" style="color:#1a1a1a;">Daftar Akun</h5>
                        <p class="text-muted small mb-4">Hanya butuh beberapa detik</p>

                        <form wire:submit="register" novalidate>
                            {{-- Nama --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold small" style="color:#374151;">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0" style="border-radius:10px 0 0 10px;">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input type="text" wire:model="nama"
                                           class="form-control border-start-0 ps-0 @error('nama') is-invalid @enderror"
                                           placeholder="Nama kamu"
                                           style="border-radius:0 10px 10px 0;">
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- No HP --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold small" style="color:#374151;">Nomor HP</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0" style="border-radius:10px 0 0 10px;">
                                        <i class="fas fa-phone text-muted"></i>
                                    </span>
                                    <input type="tel" wire:model="no_hp"
                                           class="form-control border-start-0 ps-0 @error('no_hp') is-invalid @enderror"
                                           placeholder="08xx-xxxx-xxxx"
                                           style="border-radius:0 10px 10px 0;">
                                    @error('no_hp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Gunakan nomor WhatsApp aktif</small>
                            </div>

                            {{-- Password --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold small" style="color:#374151;">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0" style="border-radius:10px 0 0 10px;">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" wire:model="password"
                                           class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror"
                                           placeholder="Min. 6 karakter"
                                           style="border-radius:0 10px 10px 0;">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Konfirmasi Password --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold small" style="color:#374151;">Konfirmasi Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0" style="border-radius:10px 0 0 10px;">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" wire:model="password_confirmation"
                                           class="form-control border-start-0 ps-0"
                                           placeholder="Ulangi password"
                                           style="border-radius:0 10px 10px 0;">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-semibold py-2"
                                    style="border-radius:10px;"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove>Daftar Sekarang</span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm me-1"></span> Mendaftar...
                                </span>
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <span class="text-muted small">Sudah punya akun?</span>
                            <a href="{{ route('customer.login') }}" wire:navigate class="small fw-semibold ms-1"
                               style="color:var(--button-bg-color);text-decoration:none;">Masuk</a>
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
