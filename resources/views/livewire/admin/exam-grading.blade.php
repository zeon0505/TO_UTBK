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

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
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
                                    wire:click="resetStudentExam({{ $res->id }})"
                                    wire:confirm="Reset ujian mahasiswa {{ $res->user->name ?? '' }}?\n\nSemua jawaban akan dihapus, waktu pengerjaan dikembalikan utuh, dan status pelanggaran dikosongkan agar mahasiswa dapat mengerjakan ulang dari awal.">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan &amp; Reset Ujian
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">Belum ada mahasiswa yang terdaftar di sesi UTS ini.</div>
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
                            <small class="text-muted">
                                NIM: <strong>{{ $selectedResult->user->nim ?? '-' }}</strong> | 
                                Kelas: <strong>{{ $selectedResult->user->kelas ?? '-' }}</strong> | 
                                Status: <strong>{{ $selectedResult->submitted_at ? 'Dikirim: ' . $selectedResult->submitted_at->format('d M Y, H:i') : 'Sedang Ujian / Belum Selesai' }}</strong>
                            </small>
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
                            <button type="button" class="btn btn-outline-danger btn-sm fw-semibold"
                                wire:click="resetStudentExam({{ $selectedResult->id }})"
                                wire:confirm="Reset ujian mahasiswa {{ $selectedResult->user->name }}?\n\nSemua jawaban akan dihapus, batas waktu kembali utuh, dan status pelanggaran dikosongkan.">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Ujian
                            </button>
                            <button class="btn btn-success fw-bold" wire:click="saveGrading">
                                <i class="bi bi-check-all me-1"></i> Simpan &amp; Publis Nilai
                            </button>
                        </div>
                    </div>

                    <!-- Mini Summary Scores -->
                    <div class="px-4 py-3 bg-light border-bottom">
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="bg-white p-2 rounded border shadow-sm">
                                    <small class="text-muted d-block">Nilai Pilihan Ganda</small>
                                    <span class="fs-5 fw-bold text-primary">{{ number_format($selectedResult->total_pg_score, 1) }}%</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-white p-2 rounded border shadow-sm">
                                    <small class="text-muted d-block">Nilai Essay / Uraian</small>
                                    <span class="fs-5 fw-bold text-warning">{{ number_format($selectedResult->total_essay_score, 1) }}%</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-white p-2 rounded border shadow-sm">
                                    <small class="text-muted d-block">Total Nilai Akhir</small>
                                    <span class="fs-5 fw-bold {{ $selectedResult->score >= 60 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($selectedResult->score, 1) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($studentCourseHistory->count() > 1)
                    <!-- Riwayat Sesi Lain pada Mata Kuliah Ini -->
                    <div class="px-4 py-2 bg-light-subtle border-bottom">
                        <small class="fw-bold text-secondary d-block mb-1">
                            <i class="bi bi-clock-history me-1"></i> Riwayat Sesi UTS Lain Mahasiswa ini di Mata Kuliah {{ $exam->course->name }}:
                        </small>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($studentCourseHistory as $hist)
                                @if($hist->id !== $selectedResult->id)
                                    <a href="/admin/grading/{{ $hist->exam_id }}?result_id={{ $hist->id }}" class="badge text-decoration-none border bg-white text-dark p-2" wire:navigate>
                                        <strong>{{ $hist->exam->title ?? 'Sesi' }}</strong>: 
                                        Nilai {{ number_format($hist->score, 1) }} 
                                        ({{ $hist->is_graded ? 'Telah Dinilai' : ($hist->submitted_at ? 'Perlu Koreksi' : 'Sedang Ujian') }})
                                        <i class="bi bi-arrow-right ms-1 text-primary"></i>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Question & Answer History -->
                    <div class="card-body p-4">
                        <form wire:submit.prevent="saveGrading">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="bi bi-card-checklist text-primary me-2"></i>Daftar Soal & Riwayat Jawaban Mahasiswa ({{ $exam->questions->count() }} Soal)
                            </h6>

                            @forelse($exam->questions as $qIndex => $q)
                            @php
                                $ans = $studentAnswers->get($q->id);
                                $studentOption = $ans?->selectedOption;
                                $isCorrect = $studentOption?->is_correct;
                            @endphp
                            <div class="p-3 mb-4 rounded-3 border {{ $q->type === 'essay' ? 'border-warning-subtle bg-warning-subtle bg-opacity-10' : 'bg-light' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-dark me-1">Soal #{{ $qIndex + 1 }}</span>
                                        @if($q->type === 'multiple_choice')
                                            <span class="badge bg-primary">Pilihan Ganda</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Essay / Uraian</span>
                                        @endif
                                        <span class="badge bg-secondary ms-1">Maks. {{ number_format($q->weight, 1) }} Poin</span>
                                    </div>
                                    <div>
                                        @if($q->type === 'multiple_choice')
                                            @if($studentOption)
                                                @if($isCorrect)
                                                    <span class="badge bg-success fs-6"><i class="bi bi-check-lg me-1"></i> {{ number_format($q->weight, 1) }} Poin (Benar)</span>
                                                @else
                                                    <span class="badge bg-danger fs-6"><i class="bi bi-x-lg me-1"></i> 0 Poin (Salah)</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary fs-6"><i class="bi bi-dash-circle me-1"></i> 0 Poin (Tidak Dijawab)</span>
                                            @endif
                                        @else
                                            <div class="d-flex align-items-center gap-2">
                                                <label class="form-label mb-0 fw-bold small text-dark">Nilai Essay:</label>
                                                <input type="number" step="0.5" class="form-control form-control-sm fw-bold border-warning text-center" style="width: 90px;" 
                                                       wire:model="essayScores.{{ $q->id }}" min="0" max="{{ $q->weight }}">
                                                <span class="small text-muted">/ {{ number_format($q->weight, 1) }} Poin</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <p class="fw-semibold text-dark mb-2">{{ $q->question_text }}</p>

                                @if($q->image)
                                    <div class="mb-3">
                                        <img src="{{ asset('storage/' . $q->image) }}" class="img-fluid rounded border shadow-sm" style="max-height: 250px;">
                                    </div>
                                @endif

                                @if($q->type === 'multiple_choice')
                                    <!-- Options List with Student Choice & Answer Key -->
                                    <div class="mt-2 d-flex flex-column gap-1.5">
                                        @foreach($q->options as $optIndex => $opt)
                                        @php
                                            $isStudentChoice = $studentOption && $studentOption->id === $opt->id;
                                            $isKeyAnswer = $opt->is_correct;
                                            $optClass = 'border bg-white text-dark';
                                            if ($isStudentChoice && $isKeyAnswer) {
                                                $optClass = 'border-success bg-success-subtle text-success fw-bold';
                                            } elseif ($isStudentChoice && !$isKeyAnswer) {
                                                $optClass = 'border-danger bg-danger-subtle text-danger fw-bold';
                                            } elseif ($isKeyAnswer) {
                                                $optClass = 'border-success bg-success-subtle text-success border-dashed';
                                            }
                                        @endphp
                                        <div class="p-2.5 rounded-3 small d-flex align-items-center justify-content-between {{ $optClass }}">
                                            <div>
                                                <span class="fw-bold me-2">{{ chr(65 + $optIndex) }}.</span>
                                                <span>{{ $opt->option_text }}</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-1">
                                                @if($isStudentChoice)
                                                    <span class="badge {{ $isKeyAnswer ? 'bg-success' : 'bg-danger' }}">
                                                        <i class="bi bi-cursor-fill me-1"></i>Jawaban Mahasiswa
                                                    </span>
                                                @endif
                                                @if($isKeyAnswer)
                                                    <span class="badge bg-success-subtle text-success border border-success">
                                                        <i class="bi bi-check-circle-fill me-1"></i>Kunci Benar
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach

                                        @if(!$studentOption)
                                        <div class="small p-2 bg-light text-muted italic rounded border mt-1">
                                            <i class="bi bi-info-circle me-1"></i> Mahasiswa tidak memilih jawaban untuk soal pilihan ganda ini.
                                        </div>
                                        @endif
                                    </div>
                                @else
                                    <!-- Essay Answer -->
                                    <div class="p-3 bg-white rounded border border-warning mb-2">
                                        <strong class="text-dark d-block mb-1"><i class="bi bi-pencil-fill text-warning me-1"></i> Teks Jawaban Essay Mahasiswa:</strong>
                                        <div class="text-dark" style="white-space: pre-line;">{{ $ans?->essay_answer ?: '(Mahasiswa belum atau tidak mengisi jawaban essay ini)' }}</div>
                                    </div>

                                    @if($q->explanation)
                                    <div class="p-2 bg-light rounded text-muted small mt-2">
                                        <strong><i class="bi bi-lightbulb-fill text-warning me-1"></i>Rubrik / Kunci Acuan Dosen:</strong> {{ $q->explanation }}
                                    </div>
                                    @endif
                                @endif
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-card-text fs-3 d-block mb-1"></i>
                                Belum ada butir soal yang dibuat untuk sesi UTS ini.
                            </div>
                            @endforelse

                            @if($exam->questions->isNotEmpty())
                            <div class="text-end pt-3 border-top">
                                <button type="submit" class="btn btn-success btn-lg fw-bold px-4">
                                    <i class="bi bi-check-circle-fill me-2"></i>Simpan &amp; Publis Nilai UTS Mahasiswa
                                </button>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
