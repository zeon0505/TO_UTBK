<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <a href="/dashboard" class="btn btn-sm btn-outline-secondary mb-2" wire:navigate><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
                <h3 class="fw-bold mb-1"><i class="bi bi-award-fill text-warning me-2"></i>Hasil & Transkrip Nilai UTS</h3>
                <p class="text-muted mb-0">Mata Kuliah: <strong>{{ $exam->course->name ?? 'MK' }}</strong> | Sesi: {{ $exam->title }}</p>
            </div>
        </div>
    </div>

    <!-- Grade Score Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden {{ $result->is_graded ? 'bg-success-subtle border-start border-5 border-success' : 'bg-warning-subtle border-start border-5 border-warning' }}">
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <span class="badge {{ $result->is_graded ? 'bg-success' : 'bg-warning text-dark' }} fs-6 mb-2">
                                {{ $result->is_graded ? 'Telah Dinilai & Dipublis' : 'Sedang Diproses Dosen (Koreksi Essay)' }}
                            </span>
                            <h2 class="fw-bold text-dark mb-1">{{ $exam->course->name }}</h2>
                            <p class="text-secondary mb-0">Dosen Pengampu: <strong>{{ $exam->lecturer->name ?? 'Dosen' }}</strong> | Dikumpulkan: {{ $result->submitted_at ? $result->submitted_at->format('d M Y, H:i') : '-' }}</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="p-3 bg-white rounded-3 shadow-sm text-center d-inline-block" style="min-width: 170px;">
                                @if(auth()->user()->isMahasiswa())
                                    <h3 class="fw-bold text-success mb-0"><i class="bi bi-check-circle-fill me-1"></i> Selesai</h3>
                                    <small class="text-muted fw-semibold">Jawaban Terkirim</small>
                                @else
                                    <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 11px;">NILAI AKHIR UTS</small>
                                    @if($result->is_graded)
                                        <h1 class="fw-bold text-success display-4 mb-0">{{ number_format($result->score, 1) }}</h1>
                                        <small class="text-muted">Skala 0 - 100</small>
                                    @else
                                        <h1 class="fw-bold text-warning display-5 mb-0">{{ number_format($result->score, 1) }}*</h1>
                                        <small class="text-muted">*Nilai PG Sementara</small>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Question Answers Breakdown -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent py-3">
            <h5 class="card-title mb-0 fw-bold"><i class="bi bi-list-check me-2 text-primary"></i>Rincian Lembar Jawaban Mahasiswa</h5>
        </div>
        <div class="card-body">
            @foreach($userAnswers as $index => $ans)
            <div class="p-4 mb-4 rounded-3 border {{ $ans->question->type === 'essay' ? 'bg-warning-subtle bg-opacity-10 border-warning-subtle' : 'bg-light' }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="badge bg-dark me-1">Soal No. {{ $index + 1 }}</span>
                        @if($ans->question->type === 'multiple_choice')
                            <span class="badge bg-primary">Pilihan Ganda</span>
                        @else
                            <span class="badge bg-warning text-dark">Essay / Uraian</span>
                        @endif
                        @if(!auth()->user()->isMahasiswa())
                            <span class="badge bg-secondary ms-1">Maks: {{ number_format($ans->question->weight, 1) }} Poin</span>
                        @endif
                    </div>
                    <div>
                        @if(auth()->user()->isMahasiswa())
                            <span class="badge bg-success fs-6"><i class="bi bi-check-circle-fill me-1"></i> Terjawab &amp; Tersimpan</span>
                        @else
                            @if($ans->question->type === 'multiple_choice')
                                @if($ans->selectedOption && $ans->selectedOption->is_correct)
                                    <span class="badge bg-success fs-6"><i class="bi bi-check-circle-fill me-1"></i> Benar (+{{ number_format($ans->question->weight, 1) }} Poin)</span>
                                @else
                                    <span class="badge bg-danger fs-6"><i class="bi bi-x-circle-fill me-1"></i> Salah (0 Poin)</span>
                                @endif
                            @else
                                @if($result->is_graded)
                                    <span class="badge bg-success fs-6"><i class="bi bi-pencil-fill me-1"></i> Nilai Essay: {{ number_format($ans->score_given ?? 0, 1) }} / {{ number_format($ans->question->weight, 1) }}</span>
                                @else
                                    <span class="badge bg-warning text-dark fs-6"><i class="bi bi-clock me-1"></i> Menunggu Penilaian Dosen</span>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>

                <p class="fw-semibold text-dark mb-3" style="white-space: pre-line;">{{ $ans->question->question_text }}</p>

                @if($ans->question->type === 'multiple_choice')
                <div class="row g-2 mb-2">
                    @foreach($ans->question->options as $oIdx => $opt)
                    @php
                        $isSelected = ($ans->selected_option_id == $opt->id);
                        $isCorrect = $opt->is_correct;
                        $isMahasiswa = auth()->user()->isMahasiswa();

                        if ($isMahasiswa) {
                            $class = $isSelected ? 'bg-primary text-white fw-bold border-primary' : 'bg-white border text-dark';
                        } else {
                            $class = 'bg-white border text-dark';
                            if ($isSelected && $isCorrect) $class = 'bg-success text-white fw-bold border-success';
                            elseif ($isSelected && !$isCorrect) $class = 'bg-danger text-white fw-bold border-danger';
                            elseif ($isCorrect) $class = 'bg-success-subtle text-success border-success fw-bold';
                        }
                    @endphp
                    <div class="col-md-6">
                        <div class="p-2.5 rounded-3 small {{ $class }}">
                            {{ chr(65 + $oIdx) }}. {{ $opt->option_text }}
                            @if($isMahasiswa)
                                @if($isSelected) (Jawaban Anda) @endif
                            @else
                                @if($isSelected && $isCorrect) (Jawaban Anda - Benar) @endif
                                @if($isSelected && !$isCorrect) (Jawaban Anda - Salah) @endif
                                @if(!$isSelected && $isCorrect) (Kunci Jawaban Benar) @endif
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-3 bg-white rounded-3 border mb-2">
                    <strong class="text-dark d-block mb-1"><i class="bi bi-pencil-square text-primary me-1"></i> Jawaban Essay Anda:</strong>
                    <div class="text-secondary" style="white-space: pre-line;">{{ $ans->essay_answer ?: '(Anda tidak mengetikkan jawaban essay)' }}</div>
                </div>
                @endif

                @if(!auth()->user()->isMahasiswa() && $ans->question->explanation)
                <div class="p-3 bg-primary-subtle text-primary rounded-3 small mt-2">
                    <strong><i class="bi bi-info-circle-fill me-1"></i> Rubrik / Pembahasan Dosen:</strong> {{ $ans->question->explanation }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
