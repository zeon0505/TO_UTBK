<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login - Portal UTS Kampus' }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .auth-card {
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div id="auth" class="w-100 py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="text-center mb-4 text-white">
                        <h2 class="fw-bold text-primary mb-1"><i class="bi bi-mortarboard-fill me-2"></i>UTS <span class="text-light">KAMPUS</span></h2>
                        <p class="text-white-50">Portal Ujian Tengah Semester & Evaluasi Akademik</p>
                    </div>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>
