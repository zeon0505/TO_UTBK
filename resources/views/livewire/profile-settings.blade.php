<div>
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold mb-1"><i class="bi bi-person-circle text-primary me-2"></i>Pengaturan Profil Saya</h3>
            <p class="text-muted mb-0">Kelola informasi data akademis dan kata sandi akun Anda.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Informasi Akun</h5>
                </div>
                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form wire:submit.prevent="updateProfile">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" class="form-control" wire:model="name">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Kampus (Tidak dapat diubah)</label>
                            <input type="email" class="form-control bg-light" wire:model="email" disabled>
                        </div>

                        @if(auth()->user()->isMahasiswa())
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">NIM (Nomor Induk Mahasiswa)</label>
                                <input type="text" class="form-control" wire:model="nim">
                                @error('nim') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Kelas</label>
                                <input type="text" class="form-control" wire:model="kelas">
                                @error('kelas') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Program Studi</label>
                                <input type="text" class="form-control" wire:model="prodi">
                                @error('prodi') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Semester</label>
                                <input type="number" class="form-control" wire:model="semester" min="1" max="14">
                                @error('semester') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        @elseif(auth()->user()->isDosen())
                        <div class="mb-3">
                            <label class="form-label fw-semibold">NIP (Nomor Induk Pegawai)</label>
                            <input type="text" class="form-control" wire:model="nip">
                            @error('nip') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Program Studi / Homebase</label>
                            <input type="text" class="form-control" wire:model="prodi">
                            @error('prodi') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        @endif

                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan Profil
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-shield-lock-fill me-2 text-warning"></i>Ubah Kata Sandi</h5>
                </div>
                <div class="card-body">
                    @if (session()->has('password_message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('password_message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form wire:submit.prevent="updatePassword">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kata Sandi Baru</label>
                            <input type="password" class="form-control" wire:model="password" placeholder="Minimal 6 karakter">
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" class="form-control" wire:model="password_confirmation" placeholder="Ulangi kata sandi baru">
                        </div>

                        <button type="submit" class="btn btn-warning text-dark fw-bold px-4">
                            <i class="bi bi-key-fill me-1"></i> Perbarui Kata Sandi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
