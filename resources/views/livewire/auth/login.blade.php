<div class="auth-card" id="loginCard">
    <h2 class="auth-card-title">Selamat Datang 👋</h2>
    <p class="auth-card-subtitle">Masuk dengan email kampus Anda untuk mengakses portal UTS.</p>

    @if (session()->has('error'))
        <div class="auth-alert">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="login" id="loginForm">
        <!-- Email -->
        <div class="mb-3">
            <label class="auth-label">Email Kampus</label>
            <div class="auth-input-group">
                <input 
                    type="email" 
                    class="auth-input" 
                    wire:model="email" 
                    placeholder="nama@kampus.ac.id"
                    autocomplete="email"
                    id="email"
                >
                <i class="bi bi-envelope auth-input-icon"></i>
            </div>
            @error('email') <p class="auth-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p> @enderror
        </div>

        <!-- Password -->
        <div class="mb-1">
            <label class="auth-label">Kata Sandi</label>
            <div class="auth-input-group">
                <input 
                    type="password" 
                    class="auth-input" 
                    wire:model="password" 
                    placeholder="••••••••"
                    autocomplete="current-password"
                    id="password"
                >
                <i class="bi bi-lock auth-input-icon"></i>
            </div>
            @error('password') <p class="auth-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="auth-btn" id="loginBtn" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="login">
                <i class="bi bi-arrow-right-circle me-2"></i>Masuk Sekarang
            </span>
            <span wire:loading wire:target="login">
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>Memproses...
            </span>
        </button>
    </form>

    <!-- Demo accounts -->
    <div class="demo-box">
        <p class="demo-box-title">Akun Demo Pengujian</p>
        <div class="demo-badges">
            <span class="demo-badge demo-badge-mhs">
                <i class="bi bi-person-fill me-1"></i>mahasiswa@kpi.com
            </span>
            <span class="demo-badge demo-badge-dsn">
                <i class="bi bi-person-badge-fill me-1"></i>dosen@kpi.com
            </span>
            <span class="demo-badge demo-badge-adm">
                <i class="bi bi-shield-fill me-1"></i>admin@kpi.com
            </span>
        </div>
        <p class="demo-pass mb-0">Password: <code>password</code></p>
    </div>

    <div class="auth-divider"></div>

    <div class="auth-footer">
        <p class="mb-0">Belum terdaftar? <a href="/register" wire:navigate>Daftar Mahasiswa/Dosen Baru</a></p>
    </div>
</div>
