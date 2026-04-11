<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTBK 2025 | Solusi Simulasi Terpercaya</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #1e293b;
            line-height: 1.5;
        }

        .navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 0;
        }

        .hero-section {
            padding: 120px 0 80px 0;
            background: linear-gradient(to bottom, #f8fafc, #ffffff);
        }

        .btn-primary {
            background-color: #4f46e5;
            border: none;
            padding: 0.8rem 2rem;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #4338ca;
        }

        .btn-outline {
            border: 2px solid #e2e8f0;
            color: #475569;
            padding: 0.8rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.2s;
        }

        .btn-outline:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }

        .feature-box {
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #fff;
            height: 100%;
            transition: 0.3s;
        }

        .feature-box:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border-color: #4f46e5;
        }

        .icon-wrapper {
            width: 50px;
            height: 50px;
            background-color: #f1f5f9;
            color: #4f46e5;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .section-title {
            font-weight: 800;
            font-size: 2.25rem;
            color: #0f172a;
            margin-bottom: 1rem;
        }

        footer {
            background: #f8fafc;
            padding: 60px 0;
            border-top: 1px solid #e2e8f0;
        }

        .badge-soft {
            background-color: #eef2ff;
            color: #4f46e5;
            font-weight: 600;
            padding: 0.4rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            display: inline-block;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-dark fs-4" href="/">
                UTBK<span class="text-primary">2025</span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-4">
                @auth
                    <a href="/dashboard" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="/login" class="text-secondary text-decoration-none fw-medium d-none d-sm-block">Login</a>
                    <a href="/register" class="btn btn-primary">Mulai Sekarang</a>
                @endauth
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge-soft">Edisi Persiapan SNBT 2025</span>
                    <h1 class="display-4 fw-bold text-slate-900 mb-4">Lulus PTN Impian dengan Persiapan Terukur.</h1>
                    <p class="text-secondary fs-5 mb-5">Platform simulasi ujian dengan sistem penilaian IRT resmi, analisis peluang masuk PTN, dan pembahasan terlengkap untuk pejuang UTBK.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="/register" class="btn btn-primary btn-lg px-4">Daftar Member</a>
                        <a href="#tentang" class="btn btn-outline btn-lg px-4">Pelajari Lebih Lanjut</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/online-learning-2559740-2144865.png" class="img-fluid" alt="UTBK 2025">
                </div>
            </div>
        </div>
    </header>

    <section id="tentang" class="py-5">
        <div class="container py-5">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <h2 class="section-title">Kenapa Memilih Kami?</h2>
                    <p class="text-secondary">Kami menyediakan ekosistem belajar yang fokus pada hasil dan akurasi skor.</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="icon-wrapper"><i class="bi bi-bar-chart-fill"></i></div>
                        <h5 class="fw-bold">Penilaian IRT Asli</h5>
                        <p class="text-secondary small mb-0">Skor Anda dihitung berdasarkan bobot tingkat kesulitan soal, sama seperti sistem penilaian Panitia Pusat UTBK.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="icon-wrapper"><i class="bi bi-search"></i></div>
                        <h5 class="fw-bold">Rasionalisasi PTN</h5>
                        <p class="text-secondary small mb-0">Analisis peluang lolos di berbagai universitas unggulan (UI, ITB, UGM) berdasarkan skor riil yang Anda peroleh.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="icon-wrapper"><i class="bi bi-shield-check"></i></div>
                        <h5 class="fw-bold">Simulasi Realistik</h5>
                        <p class="text-secondary small mb-0">Antarmuka dan sistem navigasi bab yang didesain semirip mungkin dengan aplikasi UTBK yang sesungguhnya.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container py-5 text-center">
            <h2 class="section-title mb-4">Siap untuk Mulai Belajar?</h2>
            <p class="text-secondary mb-5 fs-5">Gabung bersama ribuan siswa lainnya dan amankan kursi Anda di PTN tahun ini.</p>
            <a href="/register" class="btn btn-primary btn-lg">Daftar Gratis Sekarang</a>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row g-4 d-flex justify-content-between">
                <div class="col-md-4">
                    <h4 class="fw-bold mb-3">UTBK<span class="text-primary">2025</span></h4>
                    <p class="text-secondary small">Platform edukasi persiapan SNBT dengan standar penilaian IRT resmi. Membantu siswa meraih impian masuk Perguruan Tinggi Negeri.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <p class="text-secondary small mb-1">© 2025 Platform UTBK. All rights reserved.</p>
                    <p class="fw-bold text-slate-800 mb-0">Developed by <span class="text-primary">Fajar Pranayoga</span></p>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
