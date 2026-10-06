<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-journal-bookmark-fill text-primary me-2"></i>Kelola Data Mata Kuliah</h3>
                <p class="text-muted mb-0">Tambah, ubah, dan atur Mata Kuliah perkuliahan untuk Ujian Tengah Semester (UTS).</p>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Form Input Mata Kuliah -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title mb-0 fw-bold">
                        {{ $isEditing ? 'Ubah Data Mata Kuliah' : 'Tambah Mata Kuliah Baru' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="saveCourse">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kode Mata Kuliah</label>
                            <input type="text" class="form-control text-uppercase" wire:model="code" placeholder="contoh: IF301">
                            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Mata Kuliah</label>
                            <input type="text" class="form-control" wire:model="name" placeholder="contoh: Pemrograman Web Lanjut">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">SKS</label>
                                <input type="number" class="form-control" wire:model="sks" min="1" max="6">
                                @error('sks') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Semester</label>
                                <select class="form-select fw-semibold" wire:model="semester">
                                    @for($s = 1; $s <= 8; $s++)
                                        <option value="{{ $s }}">Semester {{ $s }}</option>
                                    @endfor
                                </select>
                                @error('semester') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Program Studi</label>
                            <select class="form-select fw-semibold" wire:model="prodi">
                                <option value="Komunikasi dan Penyiaran Islam">Komunikasi dan Penyiaran Islam (KPI)</option>
                                <option value="Hukum Tata Negara">Hukum Tata Negara (HTN)</option>
                                <option value="Pendidikan Agama Islam">Pendidikan Agama Islam (PAI)</option>
                                <option value="Ekonomi Syariah">Ekonomi Syariah (ES)</option>
                            </select>
                            @error('prodi') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold mb-0">Dosen Pengampu Utama</label>
                                <button type="button" class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-2.5 py-0.5" wire:click="openDosenModal" style="font-size:0.75rem;">
                                    <i class="bi bi-person-plus-fill me-1"></i>+ Dosen Baru
                                </button>
                            </div>
                            <select class="form-select" wire:model="lecturer_id">
                                <option value="">-- Pilih Dosen Pengampu --</option>
                                @foreach($lecturers as $dsn)
                                    <option value="{{ $dsn->id }}">{{ $dsn->name }} (NIP: {{ $dsn->nip ?? '-' }})</option>
                                @endforeach
                            </select>
                            @error('lecturer_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary fw-bold flex-grow-1">
                                <i class="bi bi-save me-1"></i> {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Mata Kuliah' }}
                            </button>
                            @if($isEditing)
                                <button type="button" class="btn btn-secondary" wire:click="resetFields">Batal</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table List Mata Kuliah -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-list-task me-2 text-primary"></i>Daftar Mata Kuliah</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Kode & Nama MK</th>
                                    <th>SKS</th>
                                    <th>Semester</th>
                                    <th>Program Studi</th>
                                    <th>Dosen Pengampu</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($courses as $c)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary font-monospace me-1">{{ $c->code }}</span>
                                        <strong class="text-dark">{{ $c->name }}</strong>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $c->sks }} SKS</span></td>
                                    <td>Sem {{ $c->semester }}</td>
                                    <td>{{ $c->prodi }}</td>
                                    <td><small class="fw-semibold text-secondary">{{ $c->lecturer->name ?? 'Belum ditentukan' }}</small></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary me-1" wire:click="editCourse({{ $c->id }})" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="confirm('Yakin hapus mata kuliah ini?') || event.stopImmediatePropagation()" wire:click="deleteCourse({{ $c->id }})" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada Mata Kuliah yang terdaftar.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ MODAL TAMBAH DOSEN BARU ══ -->
    @if($showDosenModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 10500;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person-plus-fill me-2"></i>Tambah Dosen Pengampu Baru</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeDosenModal"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="saveNewDosen">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Nama Lengkap Dosen & Gelar</label>
                            <input type="text" class="form-control" wire:model="newDosenName" placeholder="contoh: Dr. Ahmad Farhan, M.I.Kom.">
                            @error('newDosenName') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">NIP Dosen (Nomor Induk Pegawai)</label>
                            <input type="text" class="form-control" wire:model="newDosenNip" placeholder="contoh: 198501012010121001">
                            @error('newDosenNip') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Email Kampus Dosen</label>
                            <input type="email" class="form-control" wire:model="newDosenEmail" placeholder="dosen@kampus.ac.id">
                            @error('newDosenEmail') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Kata Sandi Default</label>
                            <input type="text" class="form-control font-monospace" wire:model="newDosenPassword" placeholder="password">
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light fw-semibold" wire:click="closeDosenModal">Batal</button>
                            <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                                <i class="bi bi-check-circle-fill me-1"></i>Simpan Dosen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
