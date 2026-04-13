<div>
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>👥 Monitoring Peserta</h3>
                <p class="text-subtitle text-muted">Pantau data peserta yang sudah bergabung di platform UTBK Anda.</p>
            </div>
        </div>
    </div>

    <section class="section">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="alert alert-light-success color-success alert-dismissible show fade">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-light-danger color-danger alert-dismissible show fade">
                <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Widgets... (tetap sama) -->
            <!-- Stats Widgets -->
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon purple mb-2">
                                    <i class="bi bi-people-fill text-white"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Total Peserta</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalUsers }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon blue mb-2">
                                    <i class="bi bi-person-plus-fill text-white"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Daftar (24j Terakhir)</h6>
                                <h6 class="font-extrabold mb-0">{{ $recentUsers }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Lengkap Peserta</h5>
                    <div class="form-group mb-0">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-0" 
                                   placeholder="Cari Nama atau Email..." wire:model.live="search">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3">Nama Lengkap</th>
                                <th class="py-3">Email</th>
                                <th class="py-3">Asal Sekolah</th>
                                <th class="py-3">Tgl Gabung</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md me-3">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="">
                                        </div>
                                        <div>
                                            <span class="fw-bold d-block">{{ $user->name }}</span>
                                            @if($user->is_admin) 
                                                <span class="badge bg-light-danger text-danger btn-sm" style="font-size: 0.7rem">Administrator</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="text-muted"><i class="bi bi-envelope me-1"></i> {{ $user->email }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-light-secondary text-secondary font-normal">{{ $user->school ?? 'Tidak Diisi' }}</span>
                                </td>
                                <td class="py-3">
                                    <small class="text-muted">{{ $user->created_at->format('d M Y, H:i') }}</small>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="btn-group">
                                        <button wire:click="toggleAdmin({{ $user->id }})" 
                                                wire:confirm="Ubah status akses admin user ini?"
                                                class="btn {{ $user->is_admin ? 'btn-light-danger' : 'btn-light-info' }} btn-sm rounded-pill px-2 me-1" 
                                                title="{{ $user->is_admin ? 'Cabut Akses Admin' : 'Jadikan Admin' }}">
                                            <i class="bi {{ $user->is_admin ? 'bi-shield-fill-x' : 'bi-shield-lock-fill' }}"></i>
                                        </button>
                                        <button wire:click="editUser({{ $user->id }})" class="btn btn-light-primary btn-sm rounded-pill px-2" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button wire:click="deleteUser({{ $user->id }})" 
                                                wire:confirm="Apakah Anda yakin ingin menghapus user ini? Semua data hasil tesnya akan ikut terhapus."
                                                class="btn btn-light-danger btn-sm rounded-pill px-2 ms-1" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <img src="https://illustrations.popsy.co/amber/no-results.svg" style="height: 150px" class="mb-3">
                                    <p class="text-muted">Tidak ada user yang ditemukan dengan filter tersebut.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </section>

    @if($editingUserId)
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Data Peserta</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="cancelEdit"></button>
                </div>
                <form wire:submit="updateUser">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" wire:model="editingName">
                            @error('editingName') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" wire:model="editingEmail">
                            @error('editingEmail') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Asal Sekolah</label>
                            <input type="text" class="form-control" wire:model="editingSchool">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" wire:click="cancelEdit">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <style>
        .stats-icon { width: 3rem; height: 3rem; border-radius: .5rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .stats-icon.purple { background-color: #9694ff; }
        .stats-icon.blue { background-color: #57caeb; }
        .avatar-md { width: 2.5rem; height: 2.5rem; }
        .avatar img { border-radius: 50%; width: 100%; }
        .bg-light-success { background-color: #e8fadf; }
        .bg-light-danger { background-color: #fee5e5; }
        .bg-light-primary { background-color: #e7e5ff; }
    </style>
</div>
