<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <a href="/admin/exams" class="btn btn-sm btn-outline-secondary mb-2" wire:navigate><i class="bi bi-arrow-left"></i> Kembali ke Sesi UTS</a>
                <h3 class="fw-bold mb-1"><i class="bi bi-pencil-square text-warning me-2"></i>Koreksi Penilaian UTS Mahasiswa</h3>
                <p class="text-muted mb-0">Mata Kuliah: <strong>{{ $exam->course->name ?? 'MK' }}</strong> | Sesi: {{ $exam->title }}</p>
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
        <!-- Student Submission List -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Peserta UTS ({{ count($results) }})</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($results as $res)
                        <div class="border-bottom">
                            <button type="button"
                                    class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center border-0 w-100 {{ $selectedResultId == $res->id ? 'active' : '' }}"
                                    wire:click="selectStudentResult({{ $res->id }})">
                                <div>
                                    <div class="fw-bold">{{ $res->user->name ?? 'Mahasiswa' }}</div>
                                    <small class="{{ $selectedResultId == $res->id ? 'text-white-50' : 'text-muted' }}">NIM: {{ $res->user->nim ?? '-' }} | Kelas: {{ $res->user->kelas ?? '-' }}</small>
                                </div>
                                <div class="text-end d-flex flex-column gap-1 align-items-end">
                                    @if(($res->violations_count ?? 0) >= 3)
                                        <span class="badge bg-danger"><i class="bi bi-shield-x me-1"></i>{{ $res->violations_count }}x Blokir</span>
                                    @elseif(($res->violations_count ?? 0) > 0)
                                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>{{ $res->violations_count }}x Peringatan</span>
                                    @endif
                                    @if($res->is_graded)
                                        <span class="badge bg-success">Nilai: {{ number_format($res->score, 1) }}</span>
                                    @elseif($res->submitted_at)
                                        <span class="badge bg-warning text-dark">Perlu Koreksi</span>
                                    @else
                                        <span class="badge bg-secondary">Sedang Ujian</span>
                                    @endif
                                </div>
                            </button>
                            <div class="px-3 pb-2 pt-0" style="background: rgba(220,53,69,0.07); border-top: 1px solid rgba(220,53,69,0.15);">
                                <button type="button"
                                    class="btn btn-sm btn-outline-danger w-100 mt-2 fw-semibold"
                                    onclick="confirm('Reset ujian mahasiswa ini?\n\nSemua jawaban akan dihapus, status pelanggaran dikosongkan, dan mahasiswa dapat mengerjakan kembali dari awal.') || event.stopImmediatePropagation()"
                                    wire:click="resetStudentExam({{ $res->id }})">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan &amp; Reset Ujian
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">Belum ada mahasiswa yang mengirimkan jawaban UTS.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Grading Detail -->
        <div class="col-lg-8">
            @if(!$selectedResult)
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-person-bounding-box text-muted display-4 d-block mb-2"></i>
                        <h5 class="fw-bold text-secondary">Pilih Mahasiswa dari daftar di sebelah kiri untuk melihat jawaban & memberikan nilai essay.</h5>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="card-title mb-0 fw-bold">Jawaban UTS: {{ $selectedResult->user->name }}</h5>
                            <small class="text-muted">NIM: {{ $selectedResult->user->nim ?? '-' }} | Dikirim pada: {{ $selectedResult->submitted_at ? $selectedResult->submitted_at->format('d M Y, H:i') : 'Belum selesai' }}</small>
                            @if(($selectedResult->violations_count ?? 0) > 0)
                            <div class="mt-1">
                                <span class="badge {{ ($selectedResult->violations_count ?? 0) >= 3 ? 'bg-danger' : 'bg-warning text-dark' }} me-2">
                                    <i class="bi bi-shield-exclamation me-1"></i>Pelanggaran: {{ $selectedResult->violations_count }}x
                                </span>
                                @if(($selectedResult->violations_count ?? 0) >= 3)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Ujian dihentikan otomatis</span>
                                @endif
                            </div>
                            @endif
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            @if(($selectedResult->violations_count ?? 0) > 0 || $selectedResult->submitted_at)
                            <button class="btn btn-outline-danger btn-sm fw-semibold"
                                onclick="confirm('Reset ujian mahasiswa ini? Semua jawaban dihapus & pelanggaran dikosongkan.') || event.stopImmediatePropagation()"
                                wire:click="resetStudentExam({{ $selectedResult->id }})">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Ujian
                            </button>
                            @endif
                            <button class="btn btn-success fw-bold" wire:click="saveGrading">
                                <i class="bi bi-check-all me-1"></i> Simpan &amp; Publis Nilai
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="saveGrading">
                            @foreach($studentAnswers as $aIndex => $ans)
                            <div class="p-3 mb-4 rounded-3 border {{ $ans->question->type === 'essay' ? 'border-warning-subtle bg-warning-subtle bg-opacity-10' : 'bg-light' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-dark me-1">Soal #{{ $aIndex + 1 }}</span>
                                        @if($ans->question->type === 'multiple_choice')
                                            <span class="badge bg-primary">Pilihan Ganda</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Essay / Uraian</span>
                                        @endif
                                        <span class="badge bg-secondary ms-1">Maks. {{ number_format($ans->question->weight, 1) }} Poin</span>
                                    </div>
                                    <div>
                                        @if($ans->question->type === 'multiple_choice')
                                            @if($ans->selectedOption && $ans->selectedOption->is_correct)
                                                <span class="badge bg-success fs-6"><i class="bi bi-check-lg me-1"></i> {{ number_format($ans->question->weight, 1) }} Poin (Benar)</span>
                                            @else
                                                <span class="badge bg-danger fs-6"><i class="bi bi-x-lg me-1"></i> 0 Poin (Salah)</span>
                                            @endif
                                        @else
                                            <div class="d-flex align-items-center gap-2">
                                                <label class="form-label mb-0 fw-bold small text-dark">Nilai Essay:</label>
                                                <input type="number" step="0.5" class="form-control form-control-sm fw-bold border-warning text-center" style="width: 90px;" 
                                                       wire:model="essayScores.{{ $ans->id }}" min="0" max="{{ $ans->question->weight }}">
                                                <span class="small text-muted">/ {{ number_format($ans->question->weight, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <p class="fw-semibold text-dark mb-2">{{ $ans->question->question_text }}</p>

                                @if($ans->question->type === 'multiple_choice')
                                    <div class="small p-2 bg-white rounded border">
                                        <strong>Jawaban Mahasiswa:</strong> 
                                        @if($ans->selectedOption)
                                            <span class="{{ $ans->selectedOption->is_correct ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                                                {{ $ans->selectedOption->option_text }}
                                            </span>
                                        @else
                                            <span class="text-muted italic">(Tidak Dijawab)</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="p-3 bg-white rounded border border-warning mb-2">
                                        <strong class="text-dark d-block mb-1"><i class="bi bi-pencil-fill text-warning me-1"></i> Teks Jawaban Essay Mahasiswa:</strong>
                                        <div class="text-dark" style="white-space: pre-line;">{{ $ans->essay_answer ?: '(Mahasiswa tidak memberikan jawaban essay)' }}</div>
                                    </div>

                                    @if($ans->question->explanation)
                                    <div class="p-2 bg-light rounded text-muted small">
                                        <strong>Rubrik / Acuan Dosen:</strong> {{ $ans->question->explanation }}
                                    </div>
                                    @endif
                                @endif
                            </div>
                            @endforeach

                            <div class="text-end pt-3 border-top">
                                <button type="submit" class="btn btn-success btn-lg fw-bold px-4">
                                    <i class="bi bi-check-circle-fill me-2"></i>Simpan & Publis Nilai UTS Mahasiswa
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
