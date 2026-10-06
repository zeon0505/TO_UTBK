<div class="card auth-card shadow-lg border-0">
    <div class="card-body p-4 p-md-5">
        <h4 class="card-title fw-bold mb-3 text-dark">Pendaftaran Akun Baru</h4>
        <p class="text-muted small mb-4">Lengkapi formulir untuk mengakses sistem Ujian Tengah Semester (UTS).</p>

        <form wire:submit.prevent="register">
            <div class="form-group mb-3">
                <label class="form-label text-secondary fw-semibold">Role Pengguna</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" wire:model.live="role" value="mahasiswa" id="roleMhs">
                        <label class="form-check-label fw-bold text-dark" for="roleMhs">Mahasiswa</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" wire:model.live="role" value="dosen" id="roleDsn">
                        <label class="form-check-label fw-bold text-dark" for="roleDsn">Dosen Pengampu</label>
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <label class="form-label text-secondary fw-semibold">Nama Lengkap</label>
                <input type="text" class="form-control" wire:model="name" placeholder="misal: Budi Pratama, S.Kom">
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mb-3">
                <label class="form-label text-secondary fw-semibold">Email Kampus</label>
                <input type="email" class="form-control" wire:model="email" placeholder="nama@kampus.ac.id">
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            @if($role === 'mahasiswa')
            <div class="row">
                <div class="col-md-6 form-group mb-3">
                    <label class="form-label text-secondary fw-semibold">NIM (Nomor Induk Mahasiswa)</label>
                    <input type="text" class="form-control" wire:model="nim" placeholder="misal: 2101082001">
                    @error('nim') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6 form-group mb-3">
                    <label class="form-label text-secondary fw-semibold">Kelas</label>
                    <input type="text" class="form-control" wire:model="kelas" placeholder="misal: TI-5A">
                    @error('kelas') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group mb-3">
                    <label class="form-label text-secondary fw-semibold">Program Studi</label>
                    <input type="text" class="form-control" wire:model="prodi" placeholder="misal: Teknik Informatika">
                    @error('prodi') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6 form-group mb-3">
                    <label class="form-label text-secondary fw-semibold">Semester</label>
                    <input type="number" class="form-control" wire:model="semester" min="1" max="14">
                    @error('semester') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>
            @else
            <div class="form-group mb-3">
                <label class="form-label text-secondary fw-semibold">NIP (Nomor Induk Pegawai)</label>
                <input type="text" class="form-control" wire:model="nip" placeholder="misal: 198501012010121001">
                @error('nip') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="form-group mb-3">
                <label class="form-label text-secondary fw-semibold">Program Studi / Homebase</label>
                <input type="text" class="form-control" wire:model="prodi" placeholder="misal: Teknik Informatika">
                @error('prodi') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            @endif

            <div class="form-group mb-4">
                <label class="form-label text-secondary fw-semibold">Kata Sandi</label>
                <input type="password" class="form-control" wire:model="password" placeholder="Minimal 6 karakter">
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold rounded-3 shadow-sm">
                <i class="bi bi-person-plus-fill me-2"></i>Daftarkan Akun
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="mb-0 text-muted small">Sudah memiliki akun? <a href="/login" class="fw-bold text-primary text-decoration-none" wire:navigate>Masuk ke sini</a></p>
        </div>
    </div>
</div>
