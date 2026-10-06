<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login - Portal UTS Kampus' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #auth {
            width: 100%;
            padding: 2rem 1rem;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 2.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.06);
            animation: cardIn 0.4s ease both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .auth-card.zoom-success {
            animation: zoomOut 0.45s ease forwards;
        }

        @keyframes zoomOut {
            0%   { opacity: 1; transform: scale(1); }
            100% { opacity: 0; transform: scale(1.04); }
        }
    </style>
</head>
<body>
    <div id="auth">
        <div style="max-width: 420px; margin: 0 auto;">
            <!-- Brand -->
            <div style="text-align: center; margin-bottom: 1.75rem;">
                <div style="width:44px; height:44px; background:#4f46e5; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:0.75rem;">
                    <i class="bi bi-mortarboard-fill" style="color:#fff; font-size:20px;"></i>
                </div>
                <div style="font-size:1.1rem; font-weight:700; color:#111827; letter-spacing:-0.3px;">UTS Kampus</div>
                <div style="font-size:0.8rem; color:#9ca3af; margin-top:2px;">Portal Ujian Tengah Semester</div>
            </div>

            {{ $slot }}
        </div>
    </div>

    @livewireScripts
</body>
</html>
