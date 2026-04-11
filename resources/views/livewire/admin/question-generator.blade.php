<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-file-text text-primary me-2"></i> Text Question Generator
        </h3>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-code-square me-2"></i> Paste Raw Text Soal</h5>
                </div>
                <div class="card-body p-4">
                    <form wire:submit.prevent="generate">
                        
                        <div class="row mb-4">
                            <!-- Tryout Select -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pilih Tryout / Ujian</label>
                                <select class="form-select" wire:model.live="examId">
                                    <option value="">-- Pilih --</option>
                                    @foreach($this->exams as $ex)
                                        <option value="{{ $ex->id }}">{{ $ex->title }}</option>
                                    @endforeach
                                </select>
                                @error('examId') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- SubTest Select -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pilih Materi / Sub-Tes</label>
                                <select class="form-select" wire:model="subTestId" {{ empty($examId) ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Materi --</option>
                                    @foreach($this->subTests as $st)
                                        <option value="{{ $st->id }}">{{ $st->title }}</option>
                                    @endforeach
                                </select>
                                @error('subTestId') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- API Key -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Gemini API Key</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-key text-warning"></i></span>
                                <input type="text" class="form-control" wire:model="apiKey" placeholder="AIzaSy...">
                            </div>
                            <small class="text-muted mt-1 d-block">AI pintar membutuhkan API Key (Google AI Studio). Hanya tersimpan di sesi ini.</small>
                            @error('apiKey') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Raw Text -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tempel Soal (Bisa lebih dari 1)</label>
                            <textarea class="form-control font-monospace" wire:model="rawText" rows="10" placeholder="Paste soal berantakan di sini..." style="font-size: 14px"></textarea>
                            <small class="text-muted mt-1 d-block">AI akan otomatis memisahkan pertanyaan, pilihan, dan kunci jawaban sendiri.</small>
                            @error('rawText') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary py-3 fs-5 fw-bold shadow-sm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="generate">
                                    <i class="bi bi-robot me-2"></i> Ekstrak Soal dengan AI Pintar
                                </span>
                                <span wire:loading wire:target="generate">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    AI Sedang Menganalisis & Memisahkan Soal...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 bg-light rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-spellcheck text-success me-2"></i> Panduan Format</h5>
                    <ul class="text-muted" style="line-height: 1.8">
                        <li>Soal harus diawali angka dan titik: <code>1. </code></li>
                        <li>Pilihan jawaban ganda wajib menggunakan huruf kapital dikuti kurung tutup <code>A)</code> atau titik <code>A.</code></li>
                        <li>Tambahkan bintang <code>*</code> di belakang teks jawaban yang benar agar terdeteksi sistem skor.</li>
                    </ul>
                    <hr>
                    <strong>Contoh Valid:</strong>
                    <pre class="bg-dark text-white p-2 rounded mt-2" style="font-size: 12px">
1. Berapa hasil dari 5+5?
A. 1
B. 10*
C. 5
                    </pre>
                </div>
            </div>
        </div>
    </div>
</div>
