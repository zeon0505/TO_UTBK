<div>
    <div class="page-heading">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3>📊 Analisis & Rasionalisasi</h3>
                <p class="text-subtitle text-muted">Berdasarkan hasil Tryout terbaru Anda (Skor: {{ number_format($latestResult->total_score ?? 0, 2) }} IP).</p>
            </div>
        </div>
    </div>

    @if(!$latestResult)
        <div class="card p-5 text-center shadow-sm border-0 rounded-4">
            <div class="card-body">
                <img src="https://illustrations.popsy.co/amber/no-results.svg" style="width: 200px;">
                <h4 class="mt-4">Belum Ada Data Skor</h4>
                <p class="text-muted">Silakan selesaikan minimal satu simulasi UTBK untuk mendapatkan analisis peluang masuk PTN.</p>
                <a href="/tryouts" class="btn btn-primary rounded-pill px-5 py-3 shadow" wire:navigate>Mulai Tryout Sekarang</a>
            </div>
        </div>
    @else
        <div class="row mb-4">
            <!-- Topic Breakdown / Analisis Kelemahan -->
            <div class="col-md-5">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-transparent p-4 pb-0">
                        <h5 class="fw-bold"><i class="bi bi-graph-up-arrow me-2 text-primary"></i> Analisis Per Materi</h5>
                        <p class="text-muted small">Cek bagian mana yang harus Anda pelajari lebih keras.</p>
                    </div>
                    <div class="card-body p-4">
                        @foreach($subtestStats as $title => $data)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-bold small">{{ $title }}</span>
                                    <span class="badge {{ $data['percentage'] < 50 ? 'bg-light-danger text-danger' : 'bg-light-success text-success' }} btn-sm rounded-pill">
                                        {{ $data['correct_count'] }}/{{ $data['total'] }} Benar
                                    </span>
                                </div>
                                <div class="progress" style="height: 10px; border-radius: 10px;">
                                    <div class="progress-bar {{ $data['percentage'] < 50 ? 'bg-danger' : 'bg-success' }}" 
                                         role="progressbar" 
                                         style="width: {{ $data['percentage'] }}%" 
                                         aria-valuenow="{{ $data['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="text-end mt-1">
                                    <small class="fw-bold" style="font-size: 0.7rem;">Skor IRT: {{ number_format($data['score'], 2) }}</small>
                                </div>
                            </div>
                        @endforeach

                        <div class="alert alert-light-primary p-3 rounded-4 mt-2">
                             @php $weakTopic = collect($subtestStats)->sortBy('percentage')->keys()->first(); @endphp
                             <small>💡 <b>Saran:</b> Fokus tingkatkan skor Anda di bagian <b>{{ $weakTopic }}</b> untuk mendongkrak total nilai IRT.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommendations / Rationalization -->
            <div class="col-md-7">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-transparent p-4 pb-0">
                        <h5 class="fw-bold"><i class="bi bi-mortarboard-fill me-2 text-primary"></i> Prediksi Kelulusan PTN</h5>
                        <p class="text-muted small">Jurusan yang paling berpeluang Anda dapatkan dengan skor saat ini.</p>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 px-4 py-3">Jurusan & Kampus</th>
                                        <th class="border-0 px-4 py-3 text-center">Peluang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recommendations as $rec)
                                        <tr class="align-middle">
                                            <td class="px-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="stats-icon {{ $rec->color }} me-3" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-bank text-white" style="font-size: 1rem;"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-bold d-block">{{ $rec->name }}</span>
                                                        <small class="text-muted">{{ $rec->university }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="badge bg-{{ $rec->color }} px-3 rounded-pill shadow-sm" style="font-size: 0.8rem;">
                                                    {{ $rec->probability }} {{ $rec->status }}
                                                </span>
                                                <div class="progress mt-2 mx-auto" style="height: 6px; width: 80px;">
                                                    <div class="progress-bar bg-{{ $rec->color }}" style="width: {{ $rec->probability }}"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert bg-white shadow-sm border-0 p-4 rounded-4 position-relative overflow-hidden">
             <div class="position-absolute top-0 end-0 opacity-10 m-3">
                 <i class="bi bi-shield-lock-fill display-1"></i>
             </div>
             <div class="row align-items-center">
                 <div class="col-auto">
                     <i class="bi bi-info-circle-fill text-primary display-6"></i>
                 </div>
                 <div class="col">
                     <h6 class="fw-bold mb-1">Disclaimer Prediksi UTBK</h6>
                     <p class="mb-0 text-muted small">Angka peluang ini dihitung menggunakan model prediktif berdasarkan riwayat passing grade tahun sebelumnya. Hasil sebenarnya bergantung pada performa peserta lain dan kebijakan panitia SNPMB secara real-time.</p>
                 </div>
             </div>
        </div>
    @endif

    <style>
        .stats-icon { border-radius: 12px; display: flex; align-items: center; justify-content: center; }
        .stats-icon.success { background-color: #435ebe; }
        .stats-icon.primary { background-color: #57caeb; }
        .stats-icon.warning { background-color: #ffc107; }
        .progress-bar { transition: width 1s ease-in-out; }
    </style>
</div>
