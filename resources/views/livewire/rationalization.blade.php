<div>
    <div class="page-heading">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3>Rasionalisasi Peluang PTN</h3>
                <p class="text-subtitle text-muted">Berdasarkan skor IRT tertinggi Anda ({{ number_format($latestResult->total_score ?? 0, 2) }}).</p>
            </div>
            <div class="col-md-6 text-end">
                <i class="bi bi-graph-up-arrow text-primary fs-1"></i>
            </div>
        </div>
    </div>

    @if(!$latestResult)
        <div class="card p-5 text-center shadow-sm">
            <div class="card-body">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" style="width: 200px;">
                <h4 class="mt-4">Belum Ada Data Skor</h4>
                <p class="text-muted">Silakan selesaikan minimal satu simulasi UTBK untuk mendapatkan analisis peluang masuk PTN.</p>
                <a href="/dashboard" class="btn btn-primary rounded-pill px-4" wire:navigate>Ke Dashboard</a>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-transparent p-4">
                        <h4 class="mb-0 fw-bold">Rekomendasi Jurusan & Universitas</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-lg mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 px-4 py-3">Universitas</th>
                                        <th class="border-0 px-4 py-3">Program Studi</th>
                                        <th class="border-0 px-4 py-3 text-center">Target Skor</th>
                                        <th class="border-0 px-4 py-3 text-center">Status</th>
                                        <th class="border-0 px-4 py-3 text-center">Peluang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recommendations as $rec)
                                        <tr class="align-middle">
                                            <td class="px-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-md bg-light-primary text-primary me-3">
                                                        <i class="bi bi-bank fs-5"></i>
                                                    </div>
                                                    <span class="fw-semibold">{{ $rec->university }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">{{ $rec->name }}</td>
                                            <td class="px-4 py-3 text-center fw-bold">{{ $rec->passing_grade }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="badge bg-light-{{ $rec->color }} text-{{ $rec->color }} px-3 rounded-pill" style="font-size: 0.75rem;">
                                                    {{ $rec->status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="fw-extrabold text-{{ $rec->color }}">{{ $rec->probability }}</div>
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar bg-{{ $rec->color }}" role="progressbar" style="width: {{ $rec->probability }}" aria-valuenow="{{ $rec->probability }}" aria-valuemin="0" aria-valuemax="100"></div>
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

        <div class="alert alert-info mt-4 rounded-4 shadow-sm border-0 p-4">
            <h5 class="alert-heading fw-bold"><i class="bi bi-info-circle-fill me-2"></i> Disclaimer Penting</h5>
            <p class="mb-0 small">Analisis rasionalisasi ini dihitung berdasarkan data rata-rata passing grade tahun sebelumnya dan performa IRT real-time Anda di platform ini. Data ini bersifat prediktif dan tidak menjamin kelulusan resmi di SNMPTN/SBMPTN yang dikelola oleh BP3.</p>
        </div>
    @endif
</div>
