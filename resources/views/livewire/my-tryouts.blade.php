<div>
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Riwayat Tryout Saya</h3>
                <p class="text-subtitle text-muted">Pantau progres pengerjaan simulasi UTBK Anda.</p>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            @forelse($results as $result)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card hover-up h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge {{ $result->exam->category == 'TPS' ? 'bg-info' : 'bg-warning' }}">
                                    {{ $result->exam->category }}
                                </span>
                                <small class="text-muted">{{ $result->finished_at->format('d M Y, H:i') }}</small>
                            </div>
                            <h5 class="card-title fw-bold">{{ $result->exam->title }}</h5>
                            <p class="text-muted small mb-4">{{ $result->exam->sub_category }}</p>
                            
                            <div class="d-flex justify-content-between align-items-end">
                                <div>
                                    <p class="text-muted small mb-0">Skor Akhir</p>
                                    <h3 class="text-primary fw-extrabold mb-0">{{ number_format($result->total_score, 2) }}</h3>
                                </div>
                                <a href="/review/{{ $result->id }}" class="btn btn-outline-primary btn-sm rounded-pill px-4" wire:navigate>
                                    Lihat Pembahasan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card p-5 text-center shadow-sm">
                        <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                        <h4>Belum ada riwayat pengerjaan.</h4>
                        <p class="text-muted">Ayo mulai simulasi pertama Anda hari ini!</p>
                        <div class="mt-3">
                            <a href="/dashboard" class="btn btn-primary rounded-pill px-4" wire:navigate>Ke Dashboard</a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </section>
</div>
