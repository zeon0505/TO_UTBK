<div class="card shadow-lg border-0">
    <div class="card-body p-5">
        <h4 class="card-title mb-4">Daftar Akun Baru</h4>

        <form wire:submit.prevent="register">
            <div class="form-group mb-3">
                <label class="mb-2">Nama Lengkap</label>
                <input type="text" class="form-control" wire:model="name" placeholder="John Doe">
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mb-3">
                <label class="mb-2">Email</label>
                <input type="email" class="form-control" wire:model="email" placeholder="john@example.com">
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mb-3">
                <label class="mb-2">Asal Sekolah</label>
                <input type="text" class="form-control" wire:model="school" placeholder="SMA Negeri 1 Jakarta">
                @error('school') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mb-4">
                <label class="mb-2">Password</label>
                <input type="password" class="form-control" wire:model="password" placeholder="Min. 8 karakter">
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fs-5 shadow">Buat Akun</button>
        </form>

        <div class="text-center mt-4">
            <p>Sudah punya akun? <a href="/login" class="fw-bold" wire:navigate>Masuk di sini</a></p>
        </div>
    </div>
</div>
