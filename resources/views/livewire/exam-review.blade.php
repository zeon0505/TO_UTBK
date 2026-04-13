<div>
    <div class="page-heading">
        <div class="row">
            <div class="col-md-6">
                <h3>Pembahasan: {{ $exam->title }}</h3>
                <p class="text-subtitle text-muted">Pelajari jawaban Anda dan pahami pembahasannya.</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="/tryouts" class="btn btn-secondary rounded-pill" wire:navigate>
                    <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
                </a>
            </div>
        </div>
    </div>

    <!-- SubTest Selector -->
    @if(!$exam->subTests->isEmpty())
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2">
                @foreach($exam->subTests as $idx => $st)
                    <button wire:click="setSubTest({{ $idx }})" 
                            class="btn {{ $currentSubTestIndex == $idx ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill btn-sm px-4">
                        {{ $st->title }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            @foreach($questions as $idx => $q)
                @php 
                    $userAnswer = $result->userAnswers->where('question_id', $q->id)->first();
                    $selectedOptionId = $userAnswer ? $userAnswer->option_id : null;
                    $correctOption = $q->options->sortByDesc('point')->first();
                @endphp
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Soal {{ $idx + 1 }}</h5>
                        <div class="d-flex gap-2">
                            <span class="badge bg-light-primary text-primary px-3">{{ $q->type }}</span>
                            @php
                                $diffClass = match($q->difficulty_level) {
                                    'Mudah' => 'bg-light-success text-success',
                                    'Sedang' => 'bg-light-warning text-warning',
                                    'Sulit' => 'bg-light-danger text-danger',
                                    default => 'bg-light-secondary text-secondary'
                                };
                            @endphp
                            <span class="badge {{ $diffClass }} px-3">
                                <i class="bi bi-bar-chart-fill me-1"></i> {{ $q->difficulty_level }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="fs-5 mb-4">{!! $q->text !!}</div>
                        
                        <div class="options row g-3">
                            @foreach($q->options as $option)
                                @php
                                    $isCorrect = $option->point > 0;
                                    $isSelected = $selectedOptionId == $option->id;
                                    $borderClass = 'border-light';
                                    $bgClass = '';
                                    
                                    if ($isSelected) {
                                        $borderClass = $isCorrect ? 'border-success' : 'border-danger';
                                        $bgClass = $isCorrect ? 'bg-light-success' : 'bg-light-danger';
                                    } elseif ($isCorrect) {
                                        $borderClass = 'border-success';
                                        $bgClass = 'bg-light-success opacity-50';
                                    }
                                @endphp
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-4 {{ $borderClass }} {{ $bgClass }} d-flex align-items-center">
                                        <div class="me-3 fw-bold">{{ chr(65 + $loop->index) }}</div>
                                        <div class="flex-grow-1">{{ $option->text }}</div>
                                        @if($isSelected && $isCorrect)
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                        @elseif($isSelected && !$isCorrect)
                                            <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                        @elseif($isCorrect)
                                             <i class="bi bi-check-circle text-success fs-5"></i>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Explanation Area -->
                        <div class="mt-5 p-4 bg-light rounded-4 border-start border-4 border-primary">
                            <h6 class="fw-bold text-primary mb-2"><i class="bi bi-lightbulb-fill"></i> Pembahasan:</h6>
                            <div class="text-muted">
                                @if($q->explanation)
                                    {!! $q->explanation !!}
                                @else
                                    <p class="mb-0 italic small">Belum ada pembahasan tertulis untuk soal ini. Kunci jawaban yang benar adalah pilihan yang berwarna hijau.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
