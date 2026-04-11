<div class="card shadow-lg border-0">
    <div class="card-body p-5">
        <h4 class="card-title mb-4">Masuk ke Akun</h4>
        
        @if (session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form wire:submit.prevent="login">
            <div class="form-group mb-3">
                <label class="mb-2">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" wire:model="email" placeholder="contoh@gmail.com">
                </div>
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mb-4">
                <label class="mb-2">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" wire:model="password" placeholder="••••••••">
                </div>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fs-5 shadow">Masuk Sekarang</button>
        </form>

        <div class="text-center mt-4">
            <p>Belum punya akun? <a href="/register" class="fw-bold" wire:navigate>Daftar di sini</a></p>
        </div>
    </div>
</div>
