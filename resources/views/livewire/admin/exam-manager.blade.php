<div x-data="{ 
    playSFX(type) {
        setTimeout(() => {
            const sounds = {
                click: 'https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3',
                success: 'https://assets.mixkit.co/active_storage/sfx/1435/1435-preview.mp3',
                delete: 'https://assets.mixkit.co/active_storage/sfx/256/256-preview.mp3'
            };
            try {
                const audio = new Audio(sounds[type]);
                audio.volume = 0.5;
                audio.play().catch(e => {});
            } catch (e) {}
        }, 0);
    }
}" x-on:play-sfx.window="playSFX($event.detail.type)">
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Management Tryout</h3>
                <p class="text-subtitle text-muted">Kelola daftar latihan dan simulasi UTBK.</p>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible show fade">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Form Section -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $isEdit ? 'Edit Tryout' : 'Tambah Tryout Baru' }}</h4>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label>Judul Tryout</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title" placeholder="Contoh: Tryout Mandiri #1">
                        @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>Kategori Utama</label>
                        <select class="form-select @error('category') is-invalid @enderror" wire:model="category">
                            <option value="TPS">TPS (Tes Potensi Skolastik)</option>
                            <option value="Literasi">Literasi & Penalaran</option>
                            <option value="FULL">Simulasi Akbar (FULL)</option>
                        </select>
                        @error('category') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>Sub-Tes (Jurusan)</label>
                        <select class="form-select" wire:model="sub_category">
                            <option value="">Pilih Sub-Tes</option>
                            <option value="Penalaran Umum">Penalaran Umum</option>
                            <option value="Kuantitatif">Pengetahuan Kuantitatif</option>
                            <option value="Literasi ID">Literasi Bahasa Indonesia</option>
                            <option value="Literasi EN">Literasi Bahasa Inggris</option>
                            <option value="Matematika">Penalaran Matematika</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Durasi (Menit) Total</label>
                        <input type="number" class="form-control @error('duration') is-invalid @enderror" wire:model="duration" placeholder="Contoh: 30">
                        @error('duration') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" wire:model="is_active">
                        <label class="form-check-label" for="flexSwitchCheckDefault">Status Aktif</label>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary flex-grow-1" wire:click="store">
                            {{ $isEdit ? 'Update' : 'Simpan' }}
                        </button>
                        @if($isEdit)
                            <button class="btn btn-secondary" wire:click="resetFields">Batal</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- List Section -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Tryout</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Durasi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exams as $exam)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $exam->title }}</div>
                                        <small class="text-muted">{{ $exam->sub_category }}</small>
                                    </td>
                                    <td>{{ $exam->category }}</td>
                                    <td>{{ $exam->duration }}m</td>
                                    <td>
                                        @if($exam->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Non-aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary p-1 px-2" x-on:click="playSFX('click'); $wire.openQuestionManager({{ $exam->id }})" title="Kelola Soal">
                                            <i class="bi bi-list-task"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning p-1 px-2" x-on:click="playSFX('click'); $wire.recalculateIRT({{ $exam->id }})" title="Kalkulasi IRT">
                                            <i class="bi bi-stars"></i>
                                        </button>
                                        <button class="btn btn-sm btn-info p-1 px-2" x-on:click="playSFX('click'); $wire.edit({{ $exam->id }})">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger p-1 px-2" x-on:click="playSFX('delete'); confirm('Hapus tryout ini?') ? $wire.delete({{ $exam->id }}) : null">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($isManagingQuestions)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center rounded-top">
                    <h5 class="mb-0">Daftar Soal: {{ \App\Models\Exam::find($examId)?->title }}</h5>
                    <button class="btn btn-sm btn-light" wire:click="closeQuestionManager">Tutup</button>
                </div>
                <div class="card-body">
                    @if (session()->has('question_message'))
                        <div class="alert alert-success alert-dismissible fade show mb-3">
                            {{ session('question_message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row mb-4 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Pilih Materi / Sub-Tes (TPS, Literasi, dll)</label>
                            <select class="form-select" wire:model.live="selectedSubTestId">
                                @foreach(\App\Models\SubTest::where('exam_id', $this->examId)->get() as $st)
                                    <option value="{{ $st->id }}">{{ $st->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-danger" x-on:click="confirm('INGAT! Semua soal di materi ini akan hilang permanen. Lanjutkan?') ? $wire.deleteAllInSubTest() : null">
                                <i class="bi bi-eraser-fill me-1"></i> Hapus Semua Soal di Materi Ini
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Isi Pertanyaan</th>
                                    <th width="150" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($questionsList as $index => $q)
                                <tr class="align-middle">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($editingQuestionId == $q->id)
                                            <div class="edit-form p-3 bg-light rounded-3">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Isi Pertanyaan</label>
                                                    <textarea class="form-control" wire:model="editQuestionText" rows="3"></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Pembahasan (Lengkap)</label>
                                                    <textarea class="form-control border-primary" wire:model="editExplanation" rows="5" placeholder="Tuliskan kunci jawaban dan alasan logisnya..."></textarea>
                                                </div>

                                                <div class="options-edit mb-3">
                                                    <label class="form-label fw-bold">Pilihan Jawaban & Poin</label>
                                                    <div class="row g-2">
                                                        @foreach($editOptions as $idx => $opt)
                                                            <div class="col-12 d-flex gap-2 align-items-center mb-2">
                                                                <span class="badge bg-primary px-3">{{ chr(65 + $idx) }}</span>
                                                                <input type="text" class="form-control" wire:model="editOptions.{{ $idx }}.text" placeholder="Teks Jawaban">
                                                                <input type="number" class="form-control" style="width: 80px;" wire:model="editOptions.{{ $idx }}.point" title="Poin (10 untuk Benar, 0 untuk Salah)">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Berikan poin <b>10</b> untuk jawaban yang benar dan <b>0</b> untuk yang salah.</small>
                                                </div>

                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-success px-3" wire:click="saveQuestion">Simpan Perubahan</button>
                                                    <button class="btn btn-sm btn-secondary px-3" wire:click="cancelEditQuestion">Batal</button>
                                                </div>
                                            </div>
                                        @else
                                            <div class="mb-1 fw-bold">{{ Str::limit(strip_tags($q->text), 150) }}</div>
                                            @if($q->explanation)
                                                <span class="badge bg-success small"><i class="bi bi-journal-text me-1"></i> Ada Pembahasan</span>
                                            @else
                                                <span class="badge bg-light text-muted small"><i class="bi bi-journal-x me-1"></i> Belum ada Pembahasan</span>
                                            @endif
                                            <div class="small text-muted mt-1">{{ $q->options->count() }} Pilihan Jawaban</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($editingQuestionId != $q->id)
                                            <button class="btn btn-info btn-sm text-white" wire:click="editQuestion({{ $q->id }})">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm" x-on:click="playSFX('delete'); confirm('Hapus soal ini?') ? $wire.deleteQuestion({{ $q->id }}) : null">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">Tidak ada soal di sub-tes ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
