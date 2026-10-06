<div class="auth-card" id="loginCard">

    <div style="margin-bottom:1.5rem;">
        <h1 style="font-size:1.25rem; font-weight:700; color:#111827; margin-bottom:0.25rem;">Masuk ke Portal</h1>
        <p style="font-size:0.84rem; color:#6b7280;">Gunakan email kampus yang terdaftar sebagai Mahasiswa, Dosen, atau Admin.</p>
    </div>

    @if (session()->has('error'))
        <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:0.65rem 0.9rem; color:#dc2626; font-size:0.83rem; margin-bottom:1.2rem; display:flex; align-items:center; gap:0.5rem;">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="login">
        <!-- Email -->
        <div style="margin-bottom:1rem;">
            <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.04em;">Email Kampus</label>
            <div style="position:relative;">
                <i class="bi bi-envelope" style="position:absolute; left:0.85rem; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:0.85rem; pointer-events:none;"></i>
                <input 
                    type="email" 
                    wire:model="email" 
                    placeholder="nama@kampus.ac.id"
                    autocomplete="email"
                    style="width:100%; padding:0.7rem 0.85rem 0.7rem 2.4rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.88rem; color:#111827; font-family:'Inter',sans-serif; outline:none; transition:border-color 0.15s, box-shadow 0.15s;"
                    onfocus="this.style.borderColor='#4f46e5'; this.style.boxShadow='0 0 0 3px rgba(79,70,229,0.1)';"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                >
            </div>
            @error('email') <p style="color:#dc2626; font-size:0.77rem; margin-top:0.3rem;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p> @enderror
        </div>

        <!-- Password -->
        <div style="margin-bottom:1.5rem;">
            <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.04em;">Kata Sandi</label>
            <div style="position:relative;">
                <i class="bi bi-lock" style="position:absolute; left:0.85rem; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:0.85rem; pointer-events:none;"></i>
                <input 
                    type="password" 
                    wire:model="password" 
                    placeholder="••••••••"
                    autocomplete="current-password"
                    style="width:100%; padding:0.7rem 0.85rem 0.7rem 2.4rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.88rem; color:#111827; font-family:'Inter',sans-serif; outline:none; transition:border-color 0.15s, box-shadow 0.15s;"
                    onfocus="this.style.borderColor='#4f46e5'; this.style.boxShadow='0 0 0 3px rgba(79,70,229,0.1)';"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                >
            </div>
            @error('password') <p style="color:#dc2626; font-size:0.77rem; margin-top:0.3rem;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p> @enderror
        </div>

        <!-- Submit -->
        <button 
            type="submit" 
            wire:loading.attr="disabled"
            style="width:100%; background:#4f46e5; color:#fff; border:none; border-radius:8px; padding:0.75rem; font-size:0.9rem; font-weight:600; font-family:'Inter',sans-serif; cursor:pointer; transition:background 0.15s; display:flex; align-items:center; justify-content:center; gap:0.5rem;"
            onmouseover="this.style.background='#4338ca';"
            onmouseout="this.style.background='#4f46e5';"
        >
            <span wire:loading.remove wire:target="login"><i class="bi bi-arrow-right-circle me-1"></i> Masuk Sekarang</span>
            <span wire:loading wire:target="login"><span class="spinner-border spinner-border-sm" role="status"></span> Memproses...</span>
        </button>
    </form>

    <!-- Demo accounts -->
    <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:0.9rem 1rem; margin-top:1.25rem; text-align:center;">
        <p style="font-size:0.75rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.6rem;">Akun Demo Pengujian</p>
        <div style="display:flex; flex-wrap:wrap; gap:0.4rem; justify-content:center; margin-bottom:0.5rem;">
            <span style="font-size:0.73rem; padding:0.2rem 0.6rem; background:#ecfdf5; color:#059669; border:1px solid #d1fae5; border-radius:5px; font-weight:500;">
                <i class="bi bi-person-fill me-1"></i>mahasiswa@kpi.com
            </span>
            <span style="font-size:0.73rem; padding:0.2rem 0.6rem; background:#eff6ff; color:#2563eb; border:1px solid #dbeafe; border-radius:5px; font-weight:500;">
                <i class="bi bi-person-badge-fill me-1"></i>dosen@kpi.com
            </span>
            <span style="font-size:0.73rem; padding:0.2rem 0.6rem; background:#fef2f2; color:#dc2626; border:1px solid #fecaca; border-radius:5px; font-weight:500;">
                <i class="bi bi-shield-fill me-1"></i>admin@kpi.com
            </span>
        </div>
        <p style="font-size:0.77rem; color:#9ca3af; margin:0;">Password: <code style="background:#e5e7eb; padding:0.1rem 0.35rem; border-radius:4px; font-size:0.77rem; color:#374151;">password</code></p>
    </div>

    <!-- Register link -->
    <p style="text-align:center; margin-top:1.25rem; font-size:0.82rem; color:#9ca3af; margin-bottom:0;">
        Belum terdaftar? <a href="/register" wire:navigate style="color:#4f46e5; font-weight:600; text-decoration:none;">Daftar Baru</a>
    </p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Livewire) {
            Livewire.on('login-success', () => {
                const card = document.getElementById('loginCard');
                if (card) card.classList.add('zoom-success');
            });
        }
    });
</script>
