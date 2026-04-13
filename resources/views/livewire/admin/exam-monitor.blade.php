<div wire:poll.5s>
    <div class="page-heading">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3>🛰️ Exam Control Room (Live)</h3>
                <p class="text-subtitle text-muted">Pantau aktivitas peserta ujian secara real-time. Data diperbarui setiap 5 detik.</p>
            </div>
            <div class="col-md-6 text-end">
                <span class="badge bg-danger animate__animated animate__flash animate__infinite">● LIVE MONITORING</span>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            @forelse($sessions as $session)
                <div class="col-md-6 col-xl-4 mb-4">
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden {{ $session->violation_count > 5 ? 'border-start border-5 border-danger' : 'border-start border-5 border-success' }}">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="avatar avatar-xl me-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($session->user->name) }}&background=random" alt="">
                                </div>
                                <div class="overflow-hidden">
                                    <h5 class="fw-bold mb-0 text-truncate">{{ $session->user->name }}</h5>
                                    <small class="text-muted"><i class="bi bi- Mortarboard me-1"></i> {{ $session->user->school }}</small>
                                </div>
                            </div>

                            <div class="bg-light rounded-4 p-3 mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Sedang Mengerjakan:</span>
                                    <span class="fw-bold small">{{ $session->exam->title }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Sub-Tes Aktif:</span>
                                    <span class="badge bg-primary rounded-pill">{{ $session->active_module }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Pelanggaran (Pindah Tab):</span>
                                    <span class="fw-bold {{ $session->violation_count > 3 ? 'text-danger' : 'text-success' }}">
                                        <i class="bi bi-exclamation-triangle-fill"></i> {{ $session->violation_count }}x
                                    </span>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button wire:click="kickUser({{ $session->id }})" 
                                        wire:confirm="PERINGATAN! Anda akan mengeluarkan peserta ini dan memberinya skor 0 secara otomatis. Lanjutkan?"
                                        class="btn btn-outline-danger rounded-pill fw-bold">
                                    <i class="bi bi-person-x-fill me-1"></i> DISKUALIFIKASI (KICK)
                                </button>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 py-2 text-center">
                            <small class="text-muted">Aktivitas Terakhir: {{ $session->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card p-5 text-center shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <img src="https://illustrations.popsy.co/amber/shaking-hands.svg" style="width: 250px;">
                            <h4 class="mt-4">Belum Ada Ujian Aktif</h4>
                            <p class="text-muted">Saat ini tidak ada peserta yang sedang mengerjakan ujian.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    <style>
        .card { transition: all 0.3s ease; }
        .card:hover { transform: translateY(-5px); }
    </style>
</div>
