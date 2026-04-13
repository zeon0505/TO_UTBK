<div class="container py-5">
    <div class="no-print text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary btn-lg rounded-pill shadow">
            <i class="bi bi-printer-fill me-2"></i> Cetak / Simpan PDF
        </button>
        <a href="/dashboard" class="btn btn-light btn-lg rounded-pill shadow ms-2">Kembali ke Dashboard</a>
    </div>

    <!-- Sertifikat Utama -->
    <div class="mx-auto shadow-lg bg-white p-1" style="max-width: 900px; border: 15px double #435ebe; position: relative;">
        <!-- Watermark -->
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.05; z-index: 0; pointer-events: none;">
            <i class="bi bi-patch-check-fill" style="font-size: 400px; color: #435ebe;"></i>
        </div>

        <div class="p-5 text-center" style="position: relative; z-index: 1;">
            <div class="mb-4">
                <h1 style="font-family: 'Playfair Display', serif; font-size: 3.5rem; color: #1a237e;" class="mb-0">SERTIFIKAT</h1>
                <p class="text-uppercase fw-bold tracking-widest text-muted" style="letter-spacing: 5px;">Hasil Simulasi UTBK-SNBT</p>
            </div>

            <div class="my-5">
                <p class="fs-5 italic mb-1">Diberikan Kepada :</p>
                <h2 class="fw-bold text-dark border-bottom d-inline-block px-5 pb-2 border-2 border-primary" style="font-family: 'Montserrat', sans-serif;">
                    {{ $result->user->name }}
                </h2>
                <p class="mt-2 text-muted fw-bold">{{ $result->user->school }}</p>
            </div>

            <div class="my-5">
                <p class="mb-3 fs-5">Telah menyelesaikan simulasi ujian pada platform <strong>{{ config('app.name') }}</strong></p>
                <div class="card bg-light border-0 shadow-sm mx-auto p-4 rounded-4" style="max-width: 500px;">
                    <h5 class="text-muted text-uppercase mb-1 small fw-bold">Tryout: {{ $result->exam->title }}</h5>
                    <div class="row align-items-center mt-3">
                        <div class="col-6 border-end text-center">
                            <h6 class="text-muted small mb-1">SKOR AKUMULASI</h6>
                            <h2 class="fw-extrabold text-primary mb-0">{{ number_format($result->total_score, 2) }}</h2>
                        </div>
                        <div class="col-6 text-center">
                            <h6 class="text-muted small mb-1">PREDIKAT</h6>
                            <h3 class="fw-bold mb-0 {{ $result->total_score > 600 ? 'text-success' : 'text-warning' }}">
                                {{ $result->total_score > 700 ? 'EXCELLENT' : ($result->total_score > 600 ? 'SANGAT BAIK' : 'CUKUP') }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5 pt-4">
                <div class="col-4 text-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ url()->current() }}" alt="QR Verification" class="mb-2 border p-1 bg-white">
                    <p class="small text-muted mb-0">Verifikasi Digital</p>
                </div>
                <div class="col-4"></div>
                <div class="col-4 text-center">
                    <p class="mb-5 small text-muted">Diterbitkan pada {{ $result->finished_at->format('d M Y') }}</p>
                    <div class="border-bottom border-dark mx-auto" style="width: 150px;"></div>
                    <p class="fw-bold mt-2 mb-0">SISTEM ANALISIS UTBK</p>
                    <small class="text-muted">Digital Signature Verified</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&display=swap');
    .tracking-widest { letter-spacing: 0.2em; }
</style>
