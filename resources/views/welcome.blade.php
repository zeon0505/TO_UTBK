<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Ujian Tengah Semester (UTS) Kampus</title>
    
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
                <i class="bi bi-mortarboard-fill text-primary me-2"></i>UTS<span class="text-primary">KAMPUS</span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-4">
                @auth
                    <a href="/dashboard" class="btn btn-primary">Buka Dashboard</a>
                @else
                    <a href="/login" class="text-secondary text-decoration-none fw-medium d-none d-sm-block">Masuk</a>
                    <a href="/register" class="btn btn-primary">Daftar Akun</a>
                @endauth
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge-soft">Sistem Ujian Akademik Kampus</span>
                    <h1 class="display-4 fw-bold text-slate-900 mb-4">Portal Ujian Tengah Semester Digital.</h1>
                    <p class="text-secondary fs-5 mb-5">Platform evaluasi perkuliahan terintegrasi untuk Mahasiswa & Dosen Pengampu dengan dukungan Pilihan Ganda, Essay, Timer Otomatis, dan Generator AI Gemini.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="/login" class="btn btn-primary btn-lg px-4">Masuk ke Portal</a>
                        <a href="#fitur" class="btn btn-outline btn-lg px-4">Lihat Fitur UTS</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/online-learning-2559740-2144865.png" class="img-fluid" alt="UTS Kampus">
                </div>
            </div>
        </div>
    </header>

    <section id="fitur" class="py-5">
        <div class="container py-5">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <h2 class="section-title">Fitur Unggulan UTS Kampus</h2>
                    <p class="text-secondary">Ekosistem lengkap pelaksanaan Ujian Tengah Semester yang fleksibel, akurat, dan transparan.</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="icon-wrapper"><i class="bi bi-file-earmark-code-fill"></i></div>
                        <h5 class="fw-bold">Input PG & Essay</h5>
                        <p class="text-secondary small mb-0">Dukungan tipe soal Pilihan Ganda otomatis dan Essay/Uraian dengan rubrik penskoran Dosen.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="icon-wrapper"><i class="bi bi-robot"></i></div>
                        <h5 class="fw-bold">AI Gemini Question Generator</h5>
                        <p class="text-secondary small mb-0">Generate soal perkuliahan otomatis berbasis studi kasus dan kurikulum RPS perkuliahan.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="icon-wrapper"><i class="bi bi-key-fill"></i></div>
                        <h5 class="fw-bold">Sistem Token UTS</h5>
                        <p class="text-secondary small mb-0">Keamanan pengerjaan ujian berbasis Token Akses per sesi perkuliahan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row g-4 d-flex justify-content-between">
                <div class="col-md-6">
                    <h4 class="fw-bold mb-2"><i class="bi bi-mortarboard-fill text-primary me-2"></i>UTS<span class="text-primary">KAMPUS</span></h4>
                    <p class="text-secondary small mb-0">Portal Ujian Tengah Semester & Evaluasi Akademik Perkuliahan.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-secondary small mb-0">© 2026 Portal UTS Kampus. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
