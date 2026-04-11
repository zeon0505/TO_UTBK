<div>
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Pengaturan Profil</h3>
                <p class="text-subtitle text-muted">Perbarui informasi akun dan preferensi Anda.</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h4 class="card-title">Informasi Dasar</h4>
                </div>
                <div class="card-body mt-3">
                    @if (session()->has('message'))
                        <div class="alert alert-success alert-dismissible show fade">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="form-group mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" wire:model="name">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" wire:model="email" disabled>
                        <small class="text-muted">Email tidak dapat diubah.</small>
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label">Asal Sekolah</label>
                        <input type="text" class="form-control" wire:model="school">
                        @error('school') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <button class="btn btn-primary w-100" wire:click="updateProfile">Simpan Perubahan</button>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h4 class="card-title">Keamanan</h4>
                </div>
                <div class="card-body mt-3">
                    @if (session()->has('password_message'))
                        <div class="alert alert-success alert-dismissible show fade">
                            {{ session('password_message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="form-group mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" class="form-control" wire:model="password">
                        @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" wire:model="password_confirmation">
                    </div>
                    <button class="btn btn-secondary w-100" wire:click="updatePassword">Ganti Password</button>
                </div>
            </div>
        </div>
    </div>
</div>
