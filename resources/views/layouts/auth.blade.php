<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login - Portal UTS Kampus' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0f1e;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated gradient background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 20%, rgba(99,102,241,0.25) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 80%, rgba(139,92,246,0.2) 0%, transparent 60%),
                radial-gradient(ellipse 40% 40% at 50% 50%, rgba(59,130,246,0.1) 0%, transparent 60%);
            animation: bgShift 10s ease-in-out infinite alternate;
            z-index: 0;
        }

        @keyframes bgShift {
            0%   { opacity: 1; transform: scale(1); }
            100% { opacity: 0.8; transform: scale(1.05); }
        }

        /* Floating blobs */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: blobFloat 12s ease-in-out infinite alternate;
            z-index: 0;
            pointer-events: none;
        }
        .blob-1 { width: 400px; height: 400px; background: #6366f1; top: -100px; left: -100px; }
        .blob-2 { width: 300px; height: 300px; background: #8b5cf6; bottom: -80px; right: -80px; animation-delay: 3s; }
        .blob-3 { width: 200px; height: 200px; background: #3b82f6; top: 50%; left: 60%; animation-delay: 6s; }

        @keyframes blobFloat {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, 20px) scale(1.1); }
        }

        /* Grid dots pattern */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 32px 32px;
            z-index: 0;
        }

        #auth {
            position: relative;
            z-index: 10;
            width: 100%;
            padding: 2rem 1rem;
        }

        /* Brand header */
        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon-wrap {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 8px 32px rgba(99,102,241,0.4);
            font-size: 28px;
            color: white;
        }

        .brand-title {
            font-size: 1.6rem;
            font-weight: 800;
            background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
        }

        .brand-subtitle {
            color: rgba(255,255,255,0.4);
            font-size: 0.82rem;
            font-weight: 400;
        }

        /* Auth card - glassmorphism */
        .auth-card {
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            padding: 2.5rem 2.5rem;
            box-shadow:
                0 32px 64px rgba(0,0,0,0.4),
                0 0 0 1px rgba(255,255,255,0.05) inset;
            animation: cardEntrance 0.6s cubic-bezier(0.22,1,0.36,1) both;
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Login success zoom */
        .auth-card.zoom-success {
            animation: zoomSuccess 0.5s cubic-bezier(0.22,1,0.36,1) forwards;
        }

        @keyframes zoomSuccess {
            0%   { transform: scale(1); opacity: 1; }
            50%  { transform: scale(1.04); }
            100% { transform: scale(0.92); opacity: 0; }
        }

        .auth-card-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 0.4rem;
            letter-spacing: -0.3px;
        }

        .auth-card-subtitle {
            color: rgba(255,255,255,0.4);
            font-size: 0.83rem;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        /* Form labels */
        .auth-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: rgba(255,255,255,0.55);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0.5rem;
        }

        /* Input fields */
        .auth-input-group {
            position: relative;
            margin-bottom: 1.2rem;
        }

        .auth-input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.3);
            font-size: 0.95rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .auth-input {
            width: 100%;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            color: #f1f5f9;
            font-size: 0.92rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
            outline: none;
        }

        .auth-input::placeholder { color: rgba(255,255,255,0.2); }

        .auth-input:focus {
            background: rgba(255,255,255,0.09);
            border-color: rgba(99,102,241,0.7);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
        }

        .auth-input:focus + .auth-input-icon,
        .auth-input-group:focus-within .auth-input-icon {
            color: #818cf8;
        }

        /* Submit button */
        .auth-btn {
            width: 100%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 12px;
            padding: 0.9rem;
            color: white;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.01em;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 4px 20px rgba(99,102,241,0.35);
            position: relative;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .auth-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(99,102,241,0.5);
            filter: brightness(1.1);
        }

        .auth-btn:active {
            transform: translateY(0);
            filter: brightness(0.95);
        }

        .auth-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity: 0;
            transition: opacity 0.25s;
        }

        .auth-btn:hover::after { opacity: 1; }

        /* Demo accounts */
        .demo-box {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            margin-top: 1.5rem;
            text-align: center;
        }

        .demo-box-title {
            color: rgba(255,255,255,0.35);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.75rem;
        }

        .demo-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            justify-content: center;
            margin-bottom: 0.65rem;
        }

        .demo-badge {
            font-size: 0.73rem;
            padding: 0.25rem 0.7rem;
            border-radius: 6px;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .demo-badge-mhs { background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.2); }
        .demo-badge-dsn { background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.2); }
        .demo-badge-adm { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.2); }

        .demo-pass {
            color: rgba(255,255,255,0.3);
            font-size: 0.78rem;
        }

        .demo-pass code {
            background: rgba(255,255,255,0.08);
            color: #a5b4fc;
            padding: 0.1rem 0.4rem;
            border-radius: 5px;
            font-size: 0.78rem;
        }

        /* Register link */
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
        }

        .auth-footer p {
            color: rgba(255,255,255,0.3);
            font-size: 0.82rem;
        }

        .auth-footer a {
            color: #818cf8;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .auth-footer a:hover { color: #a5b4fc; text-decoration: underline; }

        /* Alert */
        .auth-alert {
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.25);
            border-radius: 10px;
            color: #fca5a5;
            padding: 0.75rem 1rem;
            font-size: 0.84rem;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Error text */
        .auth-error {
            color: #f87171;
            font-size: 0.78rem;
            margin-top: 0.35rem;
        }

        /* Divider */
        .auth-divider {
            border-top: 1px solid rgba(255,255,255,0.07);
            margin: 1.5rem 0 0;
        }
    </style>
</head>
<body>
    <!-- Floating blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div id="auth">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">
                    <!-- Brand header -->
                    <div class="brand-header">
                        <div class="brand-icon-wrap">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div class="brand-title">UTS KAMPUS</div>
                        <div class="brand-subtitle">Portal Ujian Tengah Semester & Evaluasi Akademik</div>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

    @livewireScripts

    <script>
        // Listen for Livewire login success event to trigger zoom animation
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('login-success', () => {
                const card = document.querySelector('.auth-card');
                if (card) {
                    card.classList.add('zoom-success');
                }
            });
        });
    </script>
</body>
</html>
