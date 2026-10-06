<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-people-fill text-primary me-2"></i>Kelola Pengguna Kampus</h3>
                <p class="text-muted mb-0">Manajemen data Mahasiswa, Dosen Pengampu, dan Administrator Akademik.</p>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Form Add/Edit User -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title mb-0 fw-bold">
                        {{ $isEditing ? 'Ubah Data Pengguna' : 'Tambah Pengguna Baru' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="saveUser">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Role Pengguna</label>
                            <select class="form-select" wire:model.live="role">
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="dosen">Dosen Pengampu</option>
                                <option value="admin">Admin Akademik</option>
                                <option value="superadmin">Super Admin (Kaprodi)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" class="form-control" wire:model="name" placeholder="Nama pengguna">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Kampus</label>
                            <input type="email" class="form-control" wire:model="email" placeholder="email@kampus.ac.id">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        @if($role === 'mahasiswa')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">NIM</label>
                                <input type="text" class="form-control" wire:model="nim" placeholder="NIM">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Kelas</label>
                                <input type="text" class="form-control" wire:model="kelas" placeholder="misal: TI-5A">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Program Studi</label>
                                <select class="form-select fw-semibold" wire:model="prodi">
                                    <option value="Komunikasi dan Penyiaran Islam">Komunikasi dan Penyiaran Islam (KPI)</option>
                                    <option value="Hukum Tata Negara">Hukum Tata Negara (HTN)</option>
                                    <option value="Pendidikan Agama Islam">Pendidikan Agama Islam (PAI)</option>
                                    <option value="Ekonomi Syariah">Ekonomi Syariah (ES)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Semester</label>
                                <select class="form-select fw-semibold" wire:model="semester">
                                    @for($s = 1; $s <= 8; $s++)
                                        <option value="{{ $s }}">Semester {{ $s }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        @else
                        <div class="mb-3">
                            <label class="form-label fw-semibold">NIP (Opsional untuk Admin/Dosen)</label>
                            <input type="text" class="form-control" wire:model="nip" placeholder="NIP Pegawai">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Program Studi</label>
                            <select class="form-select fw-semibold" wire:model="prodi">
                                <option value="Komunikasi dan Penyiaran Islam">Komunikasi dan Penyiaran Islam (KPI)</option>
                                <option value="Hukum Tata Negara">Hukum Tata Negara (HTN)</option>
                                <option value="Pendidikan Agama Islam">Pendidikan Agama Islam (PAI)</option>
                                <option value="Ekonomi Syariah">Ekonomi Syariah (ES)</option>
                            </select>
                        </div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label fw-semibold">{{ $isEditing ? 'Ubah Password (Opsional)' : 'Password' }}</label>
                            <input type="password" class="form-control" wire:model="password" placeholder="{{ $isEditing ? 'Kosongkan jika tidak diubah' : 'Minimal 6 karakter' }}">
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary fw-bold flex-grow-1">
                                <i class="bi bi-save me-1"></i> {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
                            </button>
                            @if($isEditing)
                                <button type="button" class="btn btn-secondary" wire:click="resetFields">Batal</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table List Users -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h5 class="card-title mb-0 fw-bold"><i class="bi bi-list-columns me-2 text-primary"></i>Daftar Pengguna</h5>
                            <small class="text-muted">
                                @if($currentUser->role === 'superadmin')
                                    <span class="badge bg-danger-subtle text-danger border border-danger me-1"><i class="bi bi-shield-fill me-1"></i>Akses Superadmin</span> Menampilkan seluruh pengguna
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary me-1"><i class="bi bi-building me-1"></i>Prodi {{ $currentUser->prodi }}</span> Khusus pengguna prodi {{ $currentUser->prodi }}
                                @endif
                            </small>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <!-- Search -->
                            <div class="input-group input-group-sm" style="width: 180px;">
                                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" placeholder="Cari nama/NIM/NIP..." wire:model.live="search">
                            </div>

                            <!-- Filter Prodi (Superadmin Only) -->
                            @if($currentUser->role === 'superadmin')
                            <select class="form-select form-select-sm border-primary fw-bold" wire:model.live="selectedProdi" style="width: 180px;">
                                <option value="all">📚 Semua Prodi</option>
                                <option value="Komunikasi dan Penyiaran Islam">Komunikasi & Penyiaran Islam (KPI)</option>
                                <option value="Hukum Tata Negara">Hukum Tata Negara (HTN)</option>
                                <option value="Pendidikan Agama Islam">Pendidikan Agama Islam (PAI)</option>
                                <option value="Ekonomi Syariah">Ekonomi Syariah (ES)</option>
                            </select>
                            @endif

                            <!-- Filter Semester (Both Superadmin & Admin) -->
                            <select class="form-select form-select-sm border-primary fw-bold" wire:model.live="selectedSemester" style="width: 140px;">
                                <option value="all">🎓 Semua Semester</option>
                                @for($s = 1; $s <= 8; $s++)
                                    <option value="{{ $s }}">Semester {{ $s }}</option>
                                @endfor
                            </select>

                            <!-- Filter Role -->
                            <select class="form-select form-select-sm" wire:model.live="roleFilter" style="width: 130px;">
                                <option value="all">👥 Semua Role</option>
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="dosen">Dosen</option>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Super Admin</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nama & Email</th>
                                    <th>Role</th>
                                    <th>NIM / NIP</th>
                                    <th>Prodi / Kelas</th>
                                    <th>Hasil & Total Nilai UTS</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $u)
                                <tr>
                                    <td>
                                        <strong class="text-dark d-block">{{ $u->name }}</strong>
                                        <small class="text-muted">{{ $u->email }}</small>
                                    </td>
                                    <td>
                                        @if($u->role === 'superadmin')
                                            <span class="badge bg-danger">Super Admin</span>
                                        @elseif($u->role === 'admin')
                                            <span class="badge bg-warning text-dark">Admin</span>
                                        @elseif($u->role === 'dosen')
                                            <span class="badge bg-primary">Dosen</span>
                                        @else
                                            <span class="badge bg-info text-dark">Mahasiswa</span>
                                        @endif
                                    </td>
                                    <td><span class="font-monospace text-secondary fw-semibold">{{ $u->role === 'dosen' ? ($u->nip ?? '-') : ($u->nim ?? '-') }}</span></td>
                                    <td>
                                        <small class="d-block text-dark fw-semibold">{{ $u->prodi ?? '-' }}</small>
                                        @if($u->role === 'mahasiswa')
                                            <small class="text-muted">Kelas: {{ $u->kelas ?? '-' }} (Sem {{ $u->semester ?? '-' }})</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($u->role === 'mahasiswa')
                                            @php
                                                $resList = $u->results;
                                                $completedCount = $resList->count();
                                                $totalSum = $resList->sum('score');
                                                $avgVal = $completedCount > 0 ? round($resList->avg('score'), 1) : 0;
                                            @endphp
                                            @if($completedCount > 0)
                                                <span class="badge bg-success-subtle text-success border border-success fw-bold px-2 py-1" style="font-size:0.75rem;" title="Total Nilai akumulasi UTS">
                                                    <i class="bi bi-award-fill me-1"></i>Total: {{ number_format($totalSum, 1) }} (Rata-rata: {{ number_format($avgVal, 1) }})
                                                </span>
                                                <small class="d-block text-muted mt-0.5" style="font-size:0.72rem;">{{ $completedCount }} Sesi UTS Selesai</small>
                                            @else
                                                <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size:0.72rem;">
                                                    Belum Ada Nilai
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary me-1" wire:click="editUser({{ $u->id }})" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="confirm('Hapus akun pengguna ini?') || event.stopImmediatePropagation()" wire:click="deleteUser({{ $u->id }})" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Tidak ditemukan pengguna yang sesuai.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
