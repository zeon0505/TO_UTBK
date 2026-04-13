<div>
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>🤖 AI Question Generator</h3>
                <p class="text-subtitle text-muted">Buat puluhan soal berkualitas UTBK hanya dalam hitungan detik menggunakan AI.</p>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Konfigurasi AI</h5>
                        <form wire:submit.prevent="generate">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Topik / Materi</label>
                                <input type="text" class="form-control" wire:model="topic" placeholder="Contoh: Logaritma, Termodinamika, dll">
                                @error('topic') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tingkat Kesulitan</label>
                                <select class="form-select" wire:model="difficulty">
                                    <option value="Mudah">Mudah</option>
                                    <option value="Sedang">Sedang</option>
                                    <option value="Sulit">Sulit (HOTs)</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Target Sub-Tes</label>
                                <select class="form-select" wire:model="selectedSubTest">
                                    <option value="">-- Pilih Sub-Tes --</option>
                                    @foreach($subTests as $st)
                                        <option value="{{ $st->id }}">{{ $st->exam->title }} - {{ $st->title }}</option>
                                    @endforeach
                                </select>
                                @error('selectedSubTest') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="generate">🪄 Generate Soal Sekarang</span>
                                <span wire:loading wire:target="generate">
                                    <span class="spinner-border spinner-border-sm me-2"></span> Memikirkan Soal...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible show fade shadow-sm border-0 mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(!empty($generatedQuestions))
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">Pratinjau Hasil AI</h5>
                            <button wire:click="saveAll" class="btn btn-success fw-bold px-4 rounded-pill shadow-sm">
                                <i class="bi bi-cloud-arrow-up-fill me-2"></i> Simpan Semua Soal
                            </button>
                        </div>
                        <div class="card-body">
                            @foreach($generatedQuestions as $index => $q)
                                <div class="mb-4 p-3 border rounded-4 bg-light bg-opacity-50">
                                    <h6 class="fw-bold mb-3">Soal #{{ $index + 1 }}</h6>
                                    <p class="mb-3 fs-6">{{ $q['question'] }}</p>
                                    <div class="row g-2">
                                        @foreach($q['options'] as $o)
                                            <div class="col-6">
                                                <div class="p-2 border rounded-3 {{ $o['is_correct'] ? 'bg-success bg-opacity-10 border-success' : 'bg-white' }}">
                                                    <small class="d-block {{ $o['is_correct'] ? 'text-success fw-bold' : '' }}">
                                                        {{ $o['text'] }} 
                                                        @if($o['is_correct']) <i class="bi bi-check-lg ms-1"></i> @endif
                                                    </small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="card shadow-sm border-0 d-flex align-items-center justify-content-center p-5 text-center bg-light bg-opacity-25" style="border: 2px dashed #dee2e6 !important;">
                        <div class="stats-icon purple mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-robot fs-1 text-white"></i>
                        </div>
                        <h4 class="fw-bold">Belum Ada Soal</h4>
                        <p class="text-muted">Isi topik di sebelah kiri untuk mulai merancang soal dengan AI.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
