<div class="card auth-card shadow-lg border-0">
    <div class="card-body p-4 p-md-5">
        <h4 class="card-title fw-bold mb-3 text-dark">Masuk ke Portal</h4>
        <p class="text-muted small mb-4">Gunakan email kampus yang terdaftar sebagai Mahasiswa, Dosen, atau Admin.</p>
        
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="login">
            <div class="form-group mb-3">
                <label class="form-label text-secondary fw-semibold">Email Kampus</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control border-start-0 ps-0" wire:model="email" placeholder="nama@kampus.ac.id">
                </div>
                @error('email') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mb-4">
                <label class="form-label text-secondary fw-semibold">Kata Sandi</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control border-start-0 ps-0" wire:model="password" placeholder="••••••••">
                </div>
                @error('password') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold rounded-3 shadow-sm">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk Sekarang
            </button>
        </form>

        <div class="p-3 bg-light rounded-3 mt-4 text-center">
            <p class="mb-1 text-muted small fw-semibold">Akun Demo Pengujian UTS:</p>
            <div class="d-flex justify-content-center gap-2 flex-wrap text-xs">
                <span class="badge bg-info text-dark">Mahasiswa: mahasiswa@kampus.ac.id</span>
                <span class="badge bg-primary">Dosen: dosen@kampus.ac.id</span>
                <span class="badge bg-danger">Admin: admin@kampus.ac.id</span>
            </div>
            <p class="mt-1 mb-0 text-muted small">Password: <code>password</code></p>
        </div>

        <div class="text-center mt-4">
            <p class="mb-0 text-muted small">Belum terdaftar? <a href="/register" class="fw-bold text-primary text-decoration-none" wire:navigate>Daftar Mahasiswa/Dosen Baru</a></p>
        </div>
    </div>
</div>
