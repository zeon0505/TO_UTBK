<div>
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Dashboard Siswa</h3>
                <p class="text-subtitle text-muted">Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong>! Mari asah kemampuanmu hari ini.</p>
            </div>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible show fade">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stats Section -->
    <div class="row">
        <div class="col-6 col-lg-4 col-md-6">
            <div class="card shadow-sm border-0 glass-morphism">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-4 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon purple mb-2">
                                <i class="bi bi-journal-check"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-8 col-xxl-7">
                            <h6 class="text-muted font-semibold">Total Tryout</h6>
                            <h6 class="font-extrabold mb-0">{{ $stats['total_exams'] }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-md-6">
            <div class="card shadow-sm border-0 glass-morphism">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-4 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon blue mb-2">
                                <i class="bi bi-star-fill"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-8 col-xxl-7">
                            <h6 class="text-muted font-semibold">Rata-rata Skor</h6>
                            <h6 class="font-extrabold mb-0">{{ number_format($stats['average_score'], 1) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-md-6">
            <div class="card shadow-sm border-0 glass-morphism">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-4 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon green mb-2">
                                <i class="bi bi-trophy"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-8 col-xxl-7">
                            <h6 class="text-muted font-semibold">Ranking Global</h6>
                            <h6 class="font-extrabold mb-0">{{ $stats['global_rank'] }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm border-0 mb-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">Statistik Kemajuan</h4>
                    <span class="badge bg-light-primary text-primary px-3 rounded-pill small">10 Tryout Terakhir</span>
                </div>
                <div style="height: 300px;">
                    <canvas id="scoreChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
             <div class="card shadow-sm border-0 mb-4 p-4 text-white" style="background: linear-gradient(135deg, #1e00ff, #9933ff);">
                <h4 class="fw-bold mb-1 text-white">Latihan Cepat!</h4>
                <p class="small text-white-50 mb-3">Ingin fokus belajar spesifik? Pilih matkul tujuanmu:</p>
                
                <form wire:submit.prevent="startPractice">
                    <div class="mb-3">
                        <label class="form-label text-white fw-bold">Pilih Materi / Sub-Tes</label>
                        <select class="form-select border-0 shadow-sm" wire:model="selectedPracticeSubject" style="background-color: rgba(255, 255, 255, 0.9);">
                            <option value="">-- Pilih Materi --</option>
                            @foreach($this->practiceSubjects as $st)
                                <option value="{{ $st->id }}">{{ $st->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-light w-100 fw-bold text-primary shadow-sm" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="startPractice"><i class="bi bi-play-fill me-1"></i> Mulai Fokus Belajar</span>
                        <span wire:loading wire:target="startPractice">Menyiapkan Soal...</span>
                    </button>
                </form>
             </div>

             <div class="card shadow-sm border-0 mb-4 p-4">
                <h4 class="fw-bold mb-3">Tips Hari Ini</h4>
                <div class="d-flex align-items-start mb-3">
                    <div class="stats-icon purple me-3" style="width: 40px; height: 40px;"><i class="bi bi-lightbulb"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Fokus pada Kelemahan</h6>
                        <p class="text-muted small">Berdasarkan skor kamu, materi Pengetahuan Kuantitatif masih bisa ditingkatkan lagi.</p>
                    </div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="stats-icon green me-3" style="width: 40px; height: 40px;"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Manajemen Waktu</h6>
                        <p class="text-muted small">Coba kerjakan soal literasi dengan target 1 menit per soal untuk simulasi asli.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    <h4 class="fw-bold mb-0">Daftar Materi & Ujian</h4>
                    <div class="btn-group p-1 bg-light rounded-pill shadow-sm" style="font-size: 0.85rem;">
                        <button class="btn btn-sm rounded-pill px-3 {{ $filterCategory == 'All' ? 'btn-primary shadow-sm' : '' }}" wire:click="setFilter('All')">Semua</button>
                        <button class="btn btn-sm rounded-pill px-3 {{ $filterCategory == 'TPS' ? 'btn-primary shadow-sm' : '' }}" wire:click="setFilter('TPS')">TPS</button>
                        <button class="btn btn-sm rounded-pill px-3 {{ $filterCategory == 'Literasi' ? 'btn-primary shadow-sm' : '' }}" wire:click="setFilter('Literasi')">Literasi</button>
                        <button class="btn btn-sm rounded-pill px-3 {{ $filterCategory == 'FULL' ? 'btn-primary shadow-sm' : '' }}" wire:click="setFilter('FULL')">Full Simulation</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-lg">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th class="border-0">Nama Tryout</th>
                                <th class="border-0 text-center">Kategori</th>
                                <th class="border-0 text-center">Waktu</th>
                                <th class="border-0 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeExams as $exam)
                            <tr class="align-middle">
                                <td class="py-3 border-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md bg-light-primary text-primary me-3">
                                            <i class="bi bi-journal-text fs-5"></i>
                                        </div>
                                        <span class="fw-bold text-gray-800">{{ $exam->title }}</span>
                                    </div>
                                </td>
                                <td class="border-0 text-center">
                                    <span class="badge bg-light-info text-info px-3">{{ $exam->category }}</span>
                                </td>
                                <td class="border-0 text-center text-muted small">{{ $exam->duration }} Menit</td>
                                <td class="border-0 text-center">
                                    @if($exam->user_status == 'NOT_STARTED')
                                        <a href="/exam/{{ $exam->id }}" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm" wire:navigate>Mulai</a>
                                    @elseif($exam->user_status == 'IN_PROGRESS')
                                        <a href="/exam/{{ $exam->id }}?subject={{ $exam->last_subject_id }}" class="btn btn-warning btn-sm rounded-pill px-4 shadow-sm" wire:navigate>Lanjutkan</a>
                                    @elseif($exam->user_status == 'FINISHED')
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="/rationalization/{{ $exam->result_id }}" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm" wire:navigate>Hasil</a>
                                            <a href="/certificate/{{ $exam->result_id }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted small">Belum ada tryout yang tersedia untuk filter ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:navigated', () => {
            const ctx = document.getElementById('scoreChart');
            if (!ctx) return;

            // Cleanup existing chart instance if exists to prevent bug
            if (window.myDashboardChart) {
                window.myDashboardChart.destroy();
            }

            const isDark = localStorage.getItem('theme') === 'dark';
            const colors = {
                primary: '#6366f1',
                text: isDark ? '#94a3b8' : '#64748b',
                grid: isDark ? 'rgba(148, 163, 184, 0.1)' : 'rgba(0,0,0,0.05)',
                bg: isDark ? 'rgba(99, 102, 241, 0.2)' : 'rgba(99, 102, 241, 0.1)'
            };

            window.myDashboardChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($scoreDates),
                    datasets: [{
                        label: 'Skor IRT',
                        data: @json($scoreHistory),
                        fill: true,
                        borderColor: colors.primary,
                        backgroundColor: colors.bg,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                        pointBackgroundColor: colors.primary,
                        pointBorderColor: isDark ? '#1e1e2d' : '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: isDark ? '#1e1e2d' : '#fff',
                            titleColor: isDark ? '#fff' : '#1e293b',
                            bodyColor: isDark ? '#cbd5e1' : '#475569',
                            borderColor: colors.grid,
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: (context) => ` Skor: ${context.parsed.y.toFixed(2)} IP`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: colors.grid },
                            ticks: { 
                                color: colors.text,
                                font: { size: 10, weight: '500' }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { 
                                color: colors.text,
                                font: { size: 10, weight: '500' }
                            }
                        }
                    }
                }
            });
        });
    </script>
</div>
