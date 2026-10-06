<div>
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white border-0 shadow-sm overflow-hidden position-relative" style="background: linear-gradient(135deg, #435ebe 0%, #25396e 100%);">
                <div class="card-body p-4 p-md-5 position-relative z-1">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <span class="badge bg-white text-primary fw-bold mb-2">Portal UTS Kampus T.A. 2026/2027</span>
                            <h2 class="fw-bold mb-2 text-white">Selamat Datang, {{ auth()->user()->name }}!</h2>
                            <p class="mb-0 text-white-50 fs-6">
                                @if(auth()->user()->isDosen())
                                    Dosen Pengampu | NIP: <strong>{{ auth()->user()->nip ?? '-' }}</strong> | Program Studi: <strong>{{ auth()->user()->prodi ?? '-' }}</strong>
                                @elseif(auth()->user()->isAdmin())
                                    Administrator Sistem Akademik | Program Studi: <strong>{{ auth()->user()->prodi ?? 'Semua Prodi' }}</strong>
                                @else
                                    Mahasiswa | NIM: <strong>{{ auth()->user()->nim ?? '-' }}</strong> | Kelas: <strong>{{ auth()->user()->kelas ?? '-' }}</strong> | Semester: <strong>{{ auth()->user()->semester ?? '-' }}</strong>
                                @endif
                            </p>
                        </div>
                        @if(auth()->user()->isMahasiswa())
                        <div class="col-lg-4 mt-3 mt-lg-0 text-lg-end">
                            <div class="bg-white p-3 rounded-3 shadow-sm text-start text-dark">
                                <label class="form-label text-dark fw-bold mb-1"><i class="bi bi-key-fill text-primary me-1"></i> Masukkan Token UTS</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-uppercase fw-bold border-primary" wire:model="tokenInput" placeholder="Contoh: UTS123">
                                    <button class="btn btn-primary fw-bold" wire:click="startExamWithToken">
                                        Masuk <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                                @if($errorMessage)
                                    <small class="text-danger mt-1 d-block fw-semibold">{{ $errorMessage }}</small>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->isDosen() || auth()->user()->isAdmin())
    <!-- Stats Row for Dosen/Admin -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon bg-primary text-white rounded-3 p-3 me-3 d-flex align-items-center justify-content-center" style="width:54px; height:54px;">
                        <i class="bi bi-journal-bookmark-fill fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small">Mata Kuliah</h6>
                        <h3 class="fw-bold mb-0">{{ $coursesCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon bg-success text-white rounded-3 p-3 me-3 d-flex align-items-center justify-content-center" style="width:54px; height:54px;">
                        <i class="bi bi-calendar-check-fill fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small">Sesi UTS Aktif</h6>
                        <h3 class="fw-bold mb-0">{{ $examsCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon bg-info text-white rounded-3 p-3 me-3 d-flex align-items-center justify-content-center" style="width:54px; height:54px;">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small">Total Mahasiswa</h6>
                        <h3 class="fw-bold mb-0">{{ $studentsCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon bg-warning text-white rounded-3 p-3 me-3 d-flex align-items-center justify-content-center" style="width:54px; height:54px;">
                        <i class="bi bi-pencil-square fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small">Pending Koreksi</h6>
                        <h3 class="fw-bold mb-0">{{ $pendingGradingCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Quick Menu & Active Exams -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="overflow: visible !important;">
                <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center py-3 gap-2" style="overflow: visible !important; position: relative; z-index: 20;">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Daftar Sesi UTS Terbuka</h5>
                    <div class="d-flex align-items-center gap-2">
                        <!-- Filter Semester Admin -->
                        <div class="input-group input-group-sm" style="width: auto;">
                            <span class="input-group-text bg-primary text-white fw-bold"><i class="bi bi-bookmark-star-fill me-1"></i>Semester</span>
                            <select class="form-select border-primary fw-bold" wire:model.live="selectedSemester">
                                <option value="all">Semua Semester</option>
                                @for($s = 1; $s <= 8; $s++)
                                    <option value="{{ $s }}">Semester {{ $s }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Custom Smooth Animated Dropdown for Admin Sesi UTS -->
                        <div class="position-relative" x-data="{ open: false }" @click.outside="open = false">
                            <button type="button" @click="open = !open" 
                                    class="btn btn-sm btn-light border-primary fw-bold d-flex align-items-center gap-2 rounded-3 px-3 py-2 shadow-sm text-dark">
                                <i class="bi bi-funnel-fill text-primary"></i>
                                <span>
                                    @if($selectedCourseId === 'all')
                                        Semua Mata Kuliah
                                    @else
                                        @php $ac = $coursesList->firstWhere('id', $selectedCourseId); @endphp
                                        {{ $ac ? ($ac->code . ' - ' . $ac->name) : 'Semua Mata Kuliah' }}
                                    @endif
                                </span>
                                <i class="bi bi-chevron-down ms-1 small text-muted" :class="open ? 'rotate-180' : ''" style="transition: transform 0.2s ease;"></i>
                            </button>
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                 class="position-absolute end-0 mt-2 bg-white rounded-4 shadow-lg border p-2"
                                 style="min-width: 270px; max-height: 320px; overflow-y: auto; z-index: 9999; display: none;">
                                <button type="button" class="dropdown-item rounded-3 py-2 px-3 fw-bold d-flex align-items-center justify-content-between mb-1 {{ $selectedCourseId === 'all' ? 'bg-primary text-white' : 'text-dark' }}"
                                        @click="$wire.set('selectedCourseId', 'all'); open = false;">
                                    <span>📚 Semua Mata Kuliah</span>
                                    @if($selectedCourseId === 'all') <i class="bi bi-check-lg ms-2"></i> @endif
                                </button>
                                <div class="dropdown-divider my-1 opacity-25"></div>
                                @foreach($coursesList as $c)
                                <button type="button" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between mb-1 {{ $selectedCourseId == $c->id ? 'bg-primary text-white fw-bold' : 'text-dark' }}"
                                        @click="$wire.set('selectedCourseId', '{{ $c->id }}'); open = false;">
                                    <div class="text-truncate me-2">
                                        <span class="badge {{ $selectedCourseId == $c->id ? 'bg-white text-primary' : 'bg-secondary' }} me-2 font-monospace">{{ $c->code }}</span>
                                        <span>{{ $c->name }}</span>
                                    </div>
                                    @if($selectedCourseId == $c->id) <i class="bi bi-check-lg ms-2"></i> @endif
                                </button>
                                @endforeach
                            </div>
                        </div>
                        <a href="/admin/exams" class="btn btn-sm btn-outline-primary fw-bold" wire:navigate>Kelola Semua UTS</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Mata Kuliah / Judul UTS</th>
                                    <th>Token</th>
                                    <th>Jumlah Soal</th>
                                    <th>Peserta</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestExams as $ex)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-primary">{{ $ex->course->name ?? 'Mata Kuliah' }}</div>
                                        <small class="text-muted">{{ $ex->title }}</small>
                                    </td>
                                    <td><span class="badge bg-secondary font-monospace fs-6">{{ $ex->token }}</span></td>
                                    <td><span class="badge bg-info text-dark">{{ $ex->questions_count }} Soal</span></td>
                                    <td><span class="badge bg-primary">{{ $ex->results_count }} Mahasiswa</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="/admin/generator?exam_id={{ $ex->id }}" class="btn btn-outline-primary" wire:navigate title="Edit Soal"><i class="bi bi-plus-circle"></i> Soal</a>
                                            <a href="/admin/grading/{{ $ex->id }}" class="btn btn-outline-warning" wire:navigate title="Koreksi Essay"><i class="bi bi-pencil-square"></i> Koreksi</a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada jadwal UTS yang dibuat untuk Mata Kuliah ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-lightning-charge-fill me-2 text-warning"></i>Aksi Cepat Dosen / Admin</h5>
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="/admin/exams" class="btn btn-outline-primary text-start p-3 rounded-3" wire:navigate>
                        <i class="bi bi-calendar-plus-fill me-2 fs-5"></i>
                        <span class="fw-bold">Buat Sesi UTS Baru</span>
                        <div class="small text-muted ps-4">Atur durasi, token, dan jadwal UTS</div>
                    </a>
                    <a href="/admin/generator" class="btn btn-outline-success text-start p-3 rounded-3" wire:navigate>
                        <i class="bi bi-robot me-2 fs-5"></i>
                        <span class="fw-bold">Input & Generator Soal AI</span>
                        <div class="small text-muted ps-4">Input Pilihan Ganda, Essay, & Generate AI</div>
                    </a>
                    <a href="/admin/courses" class="btn btn-outline-info text-start p-3 rounded-3 text-dark" wire:navigate>
                        <i class="bi bi-journal-plus me-2 fs-5"></i>
                        <span class="fw-bold text-dark">Kelola Data Mata Kuliah</span>
                        <div class="small text-muted ps-4">Tambah Kode MK, SKS, & Semester</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Rekapitulasi Nilai Mahasiswa & PDF Export -->
    <div class="row mb-4" style="position: relative; z-index: 20;">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="overflow: visible !important;">
                <div class="card-header bg-transparent py-3" style="overflow: visible !important; position: relative; z-index: 20;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h5 class="card-title mb-0 fw-bold text-dark">
                                <i class="bi bi-file-earmark-bar-graph-fill text-success me-2"></i>Rekapitulasi Nilai UTS Mahasiswa
                            </h5>
                            <small class="text-muted">Hasil otomatis nilai Pilihan Ganda & Essay dari setiap Mahasiswa</small>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <!-- Search Input -->
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" placeholder="Cari Nama / NIM..." wire:model.live="searchMahasiswa">
                            </div>

                            <!-- Custom Smooth Animated Dropdown for Mata Kuliah Rekap -->
                            <div class="position-relative" x-data="{ open: false }" @click.outside="open = false">
                                <button type="button" @click="open = !open" 
                                        class="btn btn-sm btn-light border-primary fw-bold d-flex align-items-center gap-2 rounded-3 px-3 py-1.5 shadow-sm text-dark">
                                    <i class="bi bi-journal-bookmark-fill text-primary"></i>
                                    <span>
                                        @if($selectedCourseId === 'all')
                                            Semua Mata Kuliah
                                        @else
                                            @php $ac = $coursesList->firstWhere('id', $selectedCourseId); @endphp
                                            {{ $ac ? ($ac->code . ' - ' . $ac->name) : 'Semua Mata Kuliah' }}
                                        @endif
                                    </span>
                                    <i class="bi bi-chevron-down ms-1 small text-muted" :class="open ? 'rotate-180' : ''" style="transition: transform 0.2s ease;"></i>
                                </button>
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                     class="position-absolute end-0 mt-2 bg-white rounded-4 shadow-lg border p-2"
                                     style="min-width: 270px; max-height: 320px; overflow-y: auto; z-index: 9999; display: none;">
                                    <button type="button" class="dropdown-item rounded-3 py-2 px-3 fw-bold d-flex align-items-center justify-content-between mb-1 {{ $selectedCourseId === 'all' ? 'bg-primary text-white' : 'text-dark' }}"
                                            @click="$wire.set('selectedCourseId', 'all'); open = false;">
                                        <span>📚 Semua Mata Kuliah</span>
                                        @if($selectedCourseId === 'all') <i class="bi bi-check-lg ms-2"></i> @endif
                                    </button>
                                    <div class="dropdown-divider my-1 opacity-25"></div>
                                    @foreach($coursesList as $c)
                                    <button type="button" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between mb-1 {{ $selectedCourseId == $c->id ? 'bg-primary text-white fw-bold' : 'text-dark' }}"
                                            @click="$wire.set('selectedCourseId', '{{ $c->id }}'); open = false;">
                                        <div class="text-truncate me-2">
                                            <span class="badge {{ $selectedCourseId == $c->id ? 'bg-white text-primary' : 'bg-secondary' }} me-2 font-monospace">{{ $c->code }}</span>
                                            <span>{{ $c->name }}</span>
                                        </div>
                                        @if($selectedCourseId == $c->id) <i class="bi bi-check-lg ms-2"></i> @endif
                                    </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Filter Sesi UTS -->
                            <select class="form-select form-select-sm" wire:model.live="selectedExamId" style="width: 180px;">
                                <option value="all">Semua Sesi UTS</option>
                                @foreach($latestExams as $ex)
                                    <option value="{{ $ex->id }}">{{ $ex->course->code ?? '' }} - {{ Str::limit($ex->title, 20) }}</option>
                                @endforeach
                            </select>

                            <!-- Export PDF Button -->
                            <a href="/admin/reports/pdf?exam_id={{ $selectedExamId !== 'all' ? $selectedExamId : '' }}" target="_blank" class="btn btn-sm btn-danger fw-bold">
                                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Ekspor Laporan PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3" style="width: 50px;">No</th>
                                    <th>Mahasiswa (NIM)</th>
                                    <th>Prodi / Kelas</th>
                                    <th>Mata Kuliah & Sesi UTS</th>
                                    <th class="text-center">Nilai PG</th>
                                    <th class="text-center">Nilai Essay</th>
                                    <th class="text-center">Total Nilai</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($studentResults as $index => $res)
                                @php
                                    $score = $res->score;
                                    $gradeBadge = 'bg-success';
                                    if($score < 60) $gradeBadge = 'bg-danger';
                                    elseif($score < 75) $gradeBadge = 'bg-warning text-dark';
                                @endphp
                                <tr>
                                    <td class="ps-3 text-muted fw-bold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $res->user->name }}</div>
                                        <small class="text-primary font-monospace"><i class="bi bi-card-heading me-1"></i>NIM: {{ $res->user->nim ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $res->user->prodi ?? '-' }}</span>
                                        <div class="small text-muted">Kelas: {{ $res->user->kelas ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $res->exam->course->name ?? 'Mata Kuliah' }}</div>
                                        <small class="text-muted">{{ $res->exam->title }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary font-monospace fs-6">{{ number_format($res->mc_score, 1) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark font-monospace fs-6">{{ number_format($res->essay_score, 1) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $gradeBadge }} fs-6 px-3 py-2 fw-bold font-monospace">
                                            {{ number_format($res->score, 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($res->is_graded)
                                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>Selesai</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1"><i class="bi bi-clock-history me-1"></i>Koreksi Essay</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="/admin/grading/{{ $res->exam_id }}" class="btn btn-sm btn-outline-warning fw-bold" wire:navigate title="Koreksi Essay & Nilai">
                                            <i class="bi bi-pencil-square me-1"></i> Koreksi
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                        Belum ada data nilai hasil ujian mahasiswa untuk sesi UTS yang dipilih.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else
    <!-- Mahasiswa Dashboard -->
    <!-- Filter Mata Kuliah Bar (Smooth Animated Dropdown) -->
    <div class="row mb-4" style="position: relative; z-index: 30;">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-3 bg-white rounded-4" style="overflow: visible !important; position: relative; z-index: 30;">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-funnel-fill text-primary fs-4"></i>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Filter Berdasarkan Mata Kuliah</h6>
                            <small class="text-muted">Pisahkan tampilan Jadwal & Riwayat UTS per Mata Kuliah</small>
                        </div>
                    </div>
                    
                    <!-- Custom Smooth Dropdown -->
                    <div class="position-relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open" 
                                class="btn btn-light border-primary fw-bold d-flex align-items-center gap-2 rounded-3 px-4 py-2 shadow-sm text-dark">
                            <i class="bi bi-journal-bookmark-fill text-primary"></i>
                            <span>
                                @if($selectedCourseId === 'all')
                                    ✨ Semua Mata Kuliah
                                @else
                                    @php $ac = $coursesList->firstWhere('id', $selectedCourseId); @endphp
                                    {{ $ac ? ($ac->code . ' - ' . $ac->name) : 'Semua Mata Kuliah' }}
                                @endif
                            </span>
                            <i class="bi bi-chevron-down ms-2 small text-muted" :class="open ? 'rotate-180' : ''" style="transition: transform 0.2s ease;"></i>
                        </button>
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                             class="position-absolute end-0 mt-2 bg-white rounded-4 shadow-lg border p-2"
                             style="min-width: 280px; max-height: 320px; overflow-y: auto; z-index: 9999; display: none;">
                            <button type="button" class="dropdown-item rounded-3 py-2.5 px-3 fw-bold d-flex align-items-center justify-content-between mb-1 {{ $selectedCourseId === 'all' ? 'bg-primary text-white' : 'text-dark' }}"
                                    @click="$wire.set('selectedCourseId', 'all'); open = false;">
                                <span>📚 Semua Mata Kuliah</span>
                                @if($selectedCourseId === 'all') <i class="bi bi-check-lg ms-2"></i> @endif
                            </button>
                            <div class="dropdown-divider my-1 opacity-25"></div>
                            @foreach($coursesList as $c)
                            <button type="button" class="dropdown-item rounded-3 py-2.5 px-3 d-flex align-items-center justify-content-between mb-1 {{ $selectedCourseId == $c->id ? 'bg-primary text-white fw-bold' : 'text-dark' }}"
                                    @click="$wire.set('selectedCourseId', '{{ $c->id }}'); open = false;">
                                <div class="text-truncate me-2">
                                    <span class="badge {{ $selectedCourseId == $c->id ? 'bg-white text-primary' : 'bg-secondary' }} me-2 font-monospace">{{ $c->code }}</span>
                                    <span>{{ $c->name }}</span>
                                </div>
                                @if($selectedCourseId == $c->id) <i class="bi bi-check-lg ms-2"></i> @endif
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Active UTS Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-journal-text me-2 text-primary"></i>Jadwal UTS Aktif Hari Ini</h5>
                    @if($selectedCourseId !== 'all')
                        <span class="badge bg-primary fs-6"><i class="bi bi-filter me-1"></i>Filter Aktif</span>
                    @endif
                </div>
                <div class="card-body">
                    @forelse($activeExams as $exam)
                    <div class="border rounded-3 p-4 mb-3 hover-shadow transition" style="border-left: 5px solid #435ebe !important;">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                            <div>
                                <span class="badge bg-primary mb-1">{{ $exam->course->code ?? 'MK' }} - {{ $exam->course->name ?? 'Mata Kuliah' }}</span>
                                <h4 class="fw-bold mb-1">{{ $exam->title }}</h4>
                                <p class="text-muted small mb-0"><i class="bi bi-person-circle me-1"></i> Dosen: {{ $exam->lecturer->name ?? 'Dosen Pengampu' }}</p>
                            </div>
                            <span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="bi bi-clock-history me-1"></i> {{ $exam->duration_minutes }} Menit</span>
                        </div>
                        <p class="text-secondary small mb-3">{{ $exam->description ?? 'Tidak ada deskripsi tambahan.' }}</p>
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 border-top">
                            <span class="small text-muted"><i class="bi bi-card-checklist me-1"></i> Total {{ $exam->questions_count }} Soal (PG & Essay)</span>
                            <a href="/exam/{{ $exam->id }}" class="btn btn-primary fw-bold px-4 rounded-pill">
                                <i class="bi bi-play-circle-fill me-1"></i> Kerjakan UTS
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="bi bi-calendar2-check text-muted display-4"></i>
                        <h5 class="fw-bold mt-3 text-secondary">Tidak ada UTS yang sedang aktif secara terbuka.</h5>
                        <p class="text-muted small">Jika Anda memiliki token UTS dari dosen, gunakan kotak <strong>"Masukkan Token UTS"</strong> di bagian atas.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Student History & Results (NO NUMERIC SCORES SHOWN TO STUDENT) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-award-fill me-2 text-warning"></i>Riwayat UTS Saya</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($myResults as $res)
                        <li class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary fw-bold mb-1">{{ $res->exam->course->code ?? 'MK' }}</span>
                                    <h6 class="fw-bold mb-0 text-dark">{{ $res->exam->course->name ?? 'Mata Kuliah' }}</h6>
                                    <small class="text-muted">{{ $res->exam->title }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2 fw-bold small">
                                        <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                    </span>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-center py-4 text-muted">
                            Belum ada UTS yang diselesaikan.
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .rotate-180 { transform: rotate(180deg); }
</style>
