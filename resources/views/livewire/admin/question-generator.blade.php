<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-pencil-square text-primary me-2"></i>Input & Generator Soal UTS</h3>
                <p class="text-muted mb-0">Kelola soal Ujian Tengah Semester (Pilihan Ganda & Essay) secara manual atau otomatis dengan AI.</p>
            </div>
            @if($selectedExamId)
            <div>
                <span class="badge bg-primary fs-6 px-3 py-2">
                    <i class="bi bi-calculator me-1"></i> Total Bobot Soal: {{ number_format($totalWeight, 1) }} / 100
                </span>
            </div>
            @endif
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Exam Selector Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 bg-light rounded-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark mb-1"><i class="bi bi-filter me-1"></i> Pilih Sesi UTS Target:</label>
                    <select class="form-select border-primary fw-bold" wire:model.live="selectedExamId">
                        <option value="">-- Pilih Sesi UTS --</option>
                        @foreach($exams as $ex)
                            <option value="{{ $ex->id }}">
                                {{ $ex->course->code ?? 'MK' }} - {{ $ex->course->name ?? 'Mata Kuliah' }} | {{ $ex->title }} (Token: {{ $ex->token }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn {{ $mode === 'single' ? 'btn-primary' : 'btn-outline-primary' }} fw-bold" wire:click="setMode('single')">
                            <i class="bi bi-file-earmark-plus me-1"></i> Input Manual
                        </button>
                        <button type="button" class="btn {{ $mode === 'ai' ? 'btn-success' : 'btn-outline-success' }} fw-bold" wire:click="setMode('ai')">
                            <i class="bi bi-robot me-1"></i> Generator AI Gemini
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$selectedExamId)
        <div class="alert alert-warning text-center py-4">
            <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
            <strong>Silakan pilih Sesi UTS terlebih dahulu di atas untuk mulai menginput soal!</strong>
        </div>
    @else

    <div class="row g-4">
        <!-- Input Form Section -->
        <div class="col-lg-6">
            @if($mode === 'single')
            <!-- Form Manual / Edit -->
            <div class="card border-0 shadow-sm {{ $editingQuestionId ? 'border-start border-4 border-warning' : '' }}">
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                    @if($editingQuestionId)
                        <h5 class="card-title mb-0 fw-bold text-warning">
                            <i class="bi bi-pencil-fill me-2"></i>Edit Soal #{{ $editingQuestionId }}
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="cancelEdit">
                            <i class="bi bi-x-lg me-1"></i>Batal Edit
                        </button>
                    @else
                        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-plus-circle-fill text-primary me-2"></i>Form Input Soal UTS Manual</h5>
                    @endif
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $editingQuestionId ? 'updateQuestion' : 'saveSingleQuestion' }}">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipe Soal</label>
                                <select class="form-select" wire:model.live="questionType">
                                    <option value="multiple_choice">Pilihan Ganda (PG)</option>
                                    <option value="essay">Essay / Uraian</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Bobot Nilai Soal</label>
                                <input type="number" step="0.5" class="form-control fw-bold" wire:model="weight" min="0.5" max="100">
                                @error('weight') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Teks Pertanyaan / Studi Kasus UTS</label>
                            <textarea class="form-control" wire:model="questionText" rows="4" placeholder="Tuliskan isi pertanyaan atau skenario studi kasus..."></textarea>
                            @error('questionText') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        @if($questionType === 'multiple_choice')
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                <span>Opsi Jawaban & Kunci Jawaban Benar</span>
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addOption">+ Tambah Opsi</button>
                            </label>
                            @foreach($options as $index => $opt)
                            <div class="input-group mb-2">
                                <div class="input-group-text bg-white">
                                    <input class="form-check-input mt-0" type="radio" name="correctOption" value="{{ $index }}" wire:model="correctOptionIndex" title="Tandai sebagai Jawaban Benar">
                                </div>
                                <span class="input-group-text bg-light font-monospace fw-bold">{{ chr(65 + $index) }}</span>
                                <input type="text" class="form-control" wire:model="options.{{ $index }}.text" placeholder="Isi opsi {{ chr(65 + $index) }}">
                                @if(count($options) > 2)
                                <button class="btn btn-outline-danger" type="button" wire:click="removeOption({{ $index }})"><i class="bi bi-trash"></i></button>
                                @endif
                            </div>
                            @endforeach
                            <small class="text-muted d-block mt-1"><i class="bi bi-info-circle me-1"></i> Pilih radio button di sebelah kiri untuk menandai <strong>Kunci Jawaban Benar</strong>.</small>
                        </div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Rubrik Penilaian / Kunci Acuan Dosen</label>
                            <textarea class="form-control" wire:model="explanation" rows="2" placeholder="Catatan pembahasan atau poin rubrik penilaian..."></textarea>
                        </div>

                        @if($editingQuestionId)
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning fw-bold flex-grow-1 py-2 shadow-sm">
                                <i class="bi bi-save-fill me-2"></i>Simpan Perubahan
                            </button>
                            <button type="button" class="btn btn-outline-secondary fw-bold px-4" wire:click="cancelEdit">
                                <i class="bi bi-x-lg"></i> Batal
                            </button>
                        </div>
                        @else
                        <button type="submit" class="btn btn-primary fw-bold w-100 py-2.5 shadow-sm">
                            <i class="bi bi-save-fill me-2"></i>Simpan Soal ke UTS
                        </button>
                        @endif
                    </form>
                </div>
            </div>

            @elseif($mode === 'ai')
            <!-- Generator AI Gemini -->
            <div class="card border-0 shadow-sm border-start border-4 border-success">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title mb-0 fw-bold text-success"><i class="bi bi-robot me-2"></i>Generator Soal Otomatis dengan AI Gemini</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">AI akan membuat soal UTS standar perkuliahan (PG / Essay) secara instan beserta kunci jawaban & pembahasannya.</p>

                    <form wire:submit.prevent="generateWithAI">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Topik / Sub-Materi Perkuliahan</label>
                            <input type="text" class="form-control" wire:model="aiTopic" placeholder="contoh: Arsitektur Microservices & REST API Security">
                            @error('aiTopic') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipe Soal yang Di-generate</label>
                                <select class="form-select" wire:model="aiQuestionType">
                                    <option value="multiple_choice">Pilihan Ganda (PG)</option>
                                    <option value="essay">Essay / Uraian</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah Soal</label>
                                <input type="number" class="form-control" wire:model="aiCount" min="1" max="10">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success fw-bold w-100 py-2.5 shadow-sm" @if($isGenerating) disabled @endif>
                            @if($isGenerating)
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Sedang Meng-generate Soal dengan AI...
                            @else
                                <i class="bi bi-magic me-2"></i>Generate & Simpan Otomatis
                            @endif
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Question List View -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-list-stars text-primary me-2"></i>Daftar Soal Sesi UTS Ini</h5>
                    <span class="badge bg-secondary">{{ count($questions) }} Soal</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($questions as $qIndex => $q)
                        <div class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-dark me-1">Soal #{{ count($questions) - $qIndex }}</span>
                                    @if($q->type === 'multiple_choice')
                                        <span class="badge bg-primary">Pilihan Ganda</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Essay / Uraian</span>
                                    @endif
                                    <span class="badge bg-success ms-1">Bobot: {{ number_format($q->weight, 1) }}</span>
                                </div>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-warning py-0 px-2" wire:click="loadQuestionForEdit({{ $q->id }})" title="Edit Soal">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="confirm('Hapus soal ini?') || event.stopImmediatePropagation()" wire:click="deleteQuestion({{ $q->id }})" title="Hapus Soal">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <p class="fw-semibold mb-2 text-dark" style="white-space: pre-line;">{{ $q->question_text }}</p>

                            @if($q->type === 'multiple_choice')
                            <div class="row g-2 mb-2">
                                @foreach($q->options as $oIdx => $opt)
                                <div class="col-6">
                                    <div class="p-2 border rounded-2 small {{ $opt->is_correct ? 'bg-success-subtle border-success text-success fw-bold' : 'bg-light' }}">
                                        {{ chr(65 + $oIdx) }}. {{ $opt->option_text }}
                                        @if($opt->is_correct) <i class="bi bi-check-circle-fill ms-1"></i> @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if($q->explanation)
                            <div class="p-2 bg-light rounded-2 text-muted small mt-2">
                                <strong>Rubrik/Pembahasan:</strong> {{ $q->explanation }}
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x display-4 d-block mb-2 text-muted"></i>
                            Belum ada soal pada sesi UTS ini.<br>Gunakan form di sebelah kiri untuk menambah soal.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
