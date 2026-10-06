<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-calendar-event-fill text-primary me-2"></i>Kelola Sesi & Jadwal UTS</h3>
                <p class="text-muted mb-0">Buat jadwal Ujian Tengah Semester, atur Token Ujian, durasi, dan waktu pengerjaan.</p>
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
        <!-- Form Sesi UTS -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title mb-0 fw-bold">
                        {{ $isEditing ? 'Ubah Sesi UTS' : 'Buat Sesi UTS Baru' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="saveExam">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Mata Kuliah</label>
                            <select class="form-select" wire:model="course_id">
                                <option value="">-- Pilih Mata Kuliah --</option>
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }} (Sem {{ $c->semester }})</option>
                                @endforeach
                            </select>
                            @error('course_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Ujian UTS</label>
                            <input type="text" class="form-control" wire:model="title" placeholder="contoh: UTS Pemrograman Web Lanjut (Gasal 2026/2027)">
                            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Petunjuk / Deskripsi Ujian</label>
                            <textarea class="form-control" wire:model="description" rows="2" placeholder="Petunjuk pengerjaan soal..."></textarea>
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Durasi (Menit)</label>
                                <input type="number" class="form-control" wire:model.live="duration_minutes" min="15" max="300">
                                @error('duration_minutes') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Token Akses UTS</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-uppercase fw-bold" wire:model="token">
                                    <button class="btn btn-outline-secondary" type="button" wire:click="generateToken" title="Acak Token"><i class="bi bi-shuffle"></i></button>
                                </div>
                                @error('token') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Waktu Mulai UTS</label>
                            <input type="datetime-local" class="form-control" wire:model.live="start_time">
                            @error('start_time') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Waktu Selesai UTS</label>
                            <input type="datetime-local" class="form-control" wire:model="end_time">
                            @error('end_time') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" wire:model="randomize_questions" id="randomizeCheck">
                            <label class="form-check-label fw-semibold" for="randomizeCheck">
                                Acak urutan soal untuk setiap mahasiswa
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary fw-bold flex-grow-1">
                                <i class="bi bi-save me-1"></i> {{ $isEditing ? 'Simpan Perubahan' : 'Buat Sesi UTS' }}
                            </button>
                            @if($isEditing)
                                <button type="button" class="btn btn-secondary" wire:click="resetFields">Batal</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table List Sesi UTS -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Daftar Sesi UTS</h5>
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select form-select-sm border-primary fw-bold" wire:model.live="filterCourseId" style="max-width: 250px;">
                            <option value="all">📚 Filter: Semua Mata Kuliah</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Mata Kuliah & Judul UTS</th>
                                    <th>Token</th>
                                    <th>Durasi</th>
                                    <th>Jumlah Soal</th>
                                    <th>Peserta</th>
                                    <th class="text-end">Aksi & Kelola</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exams as $ex)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary me-1">{{ $ex->course->code ?? 'MK' }}</span>
                                        <strong class="text-dark">{{ $ex->course->name ?? '-' }}</strong>
                                        <div class="small text-muted">{{ $ex->title }}</div>
                                    </td>
                                    <td><span class="badge bg-secondary font-monospace fs-6">{{ $ex->token }}</span></td>
                                    <td><span class="badge bg-info text-dark">{{ $ex->duration_minutes }} Menit</span></td>
                                    <td><span class="badge bg-dark">{{ $ex->questions_count }} Soal</span></td>
                                    <td><span class="badge bg-success">{{ $ex->results_count }} Mhs</span></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm mb-1">
                                            <a href="/admin/generator?exam_id={{ $ex->id }}" class="btn btn-primary" wire:navigate title="Input Soal">
                                                <i class="bi bi-plus-circle-fill"></i> Soal
                                            </a>
                                            <a href="/admin/grading/{{ $ex->id }}" class="btn btn-warning text-dark" wire:navigate title="Koreksi Essay">
                                                <i class="bi bi-pencil-square"></i> Koreksi
                                            </a>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-secondary py-0 px-2" wire:click="editExam({{ $ex->id }})" title="Edit Sesi">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="confirm('Hapus sesi UTS ini?') || event.stopImmediatePropagation()" wire:click="deleteExam({{ $ex->id }})" title="Hapus Sesi">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada Sesi UTS yang dibuat.</td>
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
