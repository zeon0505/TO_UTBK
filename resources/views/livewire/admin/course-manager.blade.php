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
                                <input type="number" class="form-control" wire:model="semester" min="1" max="8">
                                @error('semester') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Program Studi</label>
                            <input type="text" class="form-control" wire:model="prodi" placeholder="contoh: Teknik Informatika">
                            @error('prodi') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Dosen Pengampu Utama</label>
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
</div>
