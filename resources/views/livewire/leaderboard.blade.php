<div>
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Global Leaderboard</h3>
                <p class="text-subtitle text-muted">Peringkat 20 Besar Seluruh Peserta UTBK 2025.</p>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-lg">
                        <thead>
                            <tr class="bg-light">
                                <th>Posisi</th>
                                <th>Nama Peserta</th>
                                <th>Asal Sekolah</th>
                                <th>Skor Akumulasi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rankings as $index => $rank)
                            <tr>
                                <td class="fw-bold">
                                    @if($index == 0)
                                        <i class="bi bi-trophy-fill text-warning fs-5"></i>
                                    @elseif($index == 1)
                                        <i class="bi bi-trophy-fill text-secondary fs-5"></i>
                                    @elseif($index == 2)
                                        <i class="bi bi-trophy-fill text-brown fs-5"></i>
                                    @else
                                        #{{ $index + 1 }}
                                    @endif
                                </td>
                                <td>{{ $rank->name }}</td>
                                <td>{{ $rank->school }}</td>
                                <td class="fw-bold text-primary">{{ number_format($rank->total_score, 2) }}</td>
                                <td>
                                    <span class="badge bg-success">Verified</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">Belum ada data peringkat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
