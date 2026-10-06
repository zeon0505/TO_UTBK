<div class="auth-card" id="loginCard">

    <div style="margin-bottom:1.25rem; text-align:center;">
        <h1 style="font-size:1.35rem; font-weight:800; color:#111827; margin-bottom:0.25rem; font-family:'Outfit','Inter',sans-serif;">Portal Ujian Kampus</h1>
        <p style="font-size:0.83rem; color:#6b7280; margin:0;">Silakan pilih jenis akun untuk masuk ke sistem</p>
    </div>

    <!-- Tab Selector: Mahasiswa vs Dosen/Admin -->
    <div style="display:flex; background:#f3f4f6; padding:0.25rem; border-radius:10px; margin-bottom:1.5rem; border:1px solid #e5e7eb;">
        <button type="button" 
                wire:click="setLoginType('mahasiswa')"
                @if($loginType === 'mahasiswa')
                    style="flex:1; padding:0.6rem 0.5rem; border:none; border-radius:8px; font-size:0.84rem; font-weight:700; font-family:'Inter',sans-serif; cursor:pointer; transition:all 0.2s; display:flex; align-items:center; justify-content:center; gap:0.4rem; background:#4f46e5; color:#ffffff; box-shadow:0 2px 8px rgba(79,70,229,0.3);"
                @else
                    style="flex:1; padding:0.6rem 0.5rem; border:none; border-radius:8px; font-size:0.84rem; font-weight:700; font-family:'Inter',sans-serif; cursor:pointer; transition:all 0.2s; display:flex; align-items:center; justify-content:center; gap:0.4rem; background:transparent; color:#6b7280;"
                @endif
        >
            <i class="bi bi-mortarboard-fill"></i>
            <span>Mahasiswa</span>
        </button>
        <button type="button" 
                wire:click="setLoginType('admin')"
                @if($loginType === 'admin')
                    style="flex:1; padding:0.6rem 0.5rem; border:none; border-radius:8px; font-size:0.84rem; font-weight:700; font-family:'Inter',sans-serif; cursor:pointer; transition:all 0.2s; display:flex; align-items:center; justify-content:center; gap:0.4rem; background:#4f46e5; color:#ffffff; box-shadow:0 2px 8px rgba(79,70,229,0.3);"
                @else
                    style="flex:1; padding:0.6rem 0.5rem; border:none; border-radius:8px; font-size:0.84rem; font-weight:700; font-family:'Inter',sans-serif; cursor:pointer; transition:all 0.2s; display:flex; align-items:center; justify-content:center; gap:0.4rem; background:transparent; color:#6b7280;"
                @endif
        >
            <i class="bi bi-shield-lock-fill"></i>
            <span>Dosen / Admin</span>
        </button>
    </div>

    @if (session()->has('error'))
        <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:0.65rem 0.9rem; color:#dc2626; font-size:0.83rem; margin-bottom:1.2rem; display:flex; align-items:center; gap:0.5rem;">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- 🎓 FORM LOGIN MAHASISWA -->
    @if($loginType === 'mahasiswa')
    <form wire:submit.prevent="loginMahasiswa">
        <!-- Nama Lengkap -->
        <div style="margin-bottom:0.9rem;">
            <label style="display:block; font-size:0.75rem; font-weight:700; color:#374151; margin-bottom:0.35rem; text-transform:uppercase; letter-spacing:0.04em;">Nama Lengkap</label>
            <div style="position:relative;">
                <i class="bi bi-person" style="position:absolute; left:0.85rem; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:0.85rem; pointer-events:none;"></i>
                <input 
                    type="text" 
                    wire:model="name" 
                    placeholder="Contoh: Ahmad Fauzi"
                    style="width:100%; padding:0.65rem 0.85rem 0.65rem 2.3rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.86rem; color:#111827; font-family:'Inter',sans-serif; outline:none; transition:all 0.15s;"
                    onfocus="this.style.borderColor='#4f46e5'; this.style.boxShadow='0 0 0 3px rgba(79,70,229,0.1)';"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                >
            </div>
            @error('name') <p style="color:#dc2626; font-size:0.76rem; margin-top:0.25rem; margin-bottom:0;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p> @enderror
        </div>

        <!-- NIM -->
        <div style="margin-bottom:0.9rem;">
            <label style="display:block; font-size:0.75rem; font-weight:700; color:#374151; margin-bottom:0.35rem; text-transform:uppercase; letter-spacing:0.04em;">NIM (Nomor Induk Mahasiswa)</label>
            <div style="position:relative;">
                <i class="bi bi-card-heading" style="position:absolute; left:0.85rem; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:0.85rem; pointer-events:none;"></i>
                <input 
                    type="text" 
                    wire:model="nim" 
                    placeholder="Contoh: 210101001"
                    style="width:100%; padding:0.65rem 0.85rem 0.65rem 2.3rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.86rem; color:#111827; font-family:'Inter',sans-serif; outline:none; transition:all 0.15s;"
                    onfocus="this.style.borderColor='#4f46e5'; this.style.boxShadow='0 0 0 3px rgba(79,70,229,0.1)';"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                >
            </div>
            @error('nim') <p style="color:#dc2626; font-size:0.76rem; margin-top:0.25rem; margin-bottom:0;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p> @enderror
        </div>

        <!-- Row: Prodi & Semester -->
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:0.75rem; margin-bottom:0.9rem;">
            <!-- Program Studi -->
            <div>
                <label style="display:block; font-size:0.75rem; font-weight:700; color:#374151; margin-bottom:0.35rem; text-transform:uppercase; letter-spacing:0.04em;">Program Studi</label>
                <select 
                    wire:model="prodi"
                    style="width:100%; padding:0.65rem 0.65rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.82rem; color:#111827; font-family:'Inter',sans-serif; outline:none; background:#ffffff;"
                    onfocus="this.style.borderColor='#4f46e5';"
                    onblur="this.style.borderColor='#d1d5db';"
                >
                    <option value="Komunikasi dan Penyiaran Islam">Komunikasi dan Penyiaran Islam (KPI)</option>
                    <option value="Hukum Tata Negara">Hukum Tata Negara (HTN)</option>
                    <option value="Pendidikan Agama Islam">Pendidikan Agama Islam (PAI)</option>
                    <option value="Ekonomi Syariah">Ekonomi Syariah (ES)</option>
                </select>
                @error('prodi') <p style="color:#dc2626; font-size:0.76rem; margin-top:0.25rem; margin-bottom:0;">{{ $message }}</p> @enderror
            </div>

            <!-- Semester -->
            <div>
                <label style="display:block; font-size:0.75rem; font-weight:700; color:#374151; margin-bottom:0.35rem; text-transform:uppercase; letter-spacing:0.04em;">Semester</label>
                <select 
                    wire:model="semester"
                    style="width:100%; padding:0.65rem 0.65rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.82rem; color:#111827; font-family:'Inter',sans-serif; outline:none; background:#ffffff;"
                    onfocus="this.style.borderColor='#4f46e5';"
                    onblur="this.style.borderColor='#d1d5db';"
                >
                    @for($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}">Semester {{ $s }}</option>
                    @endfor
                </select>
                @error('semester') <p style="color:#dc2626; font-size:0.76rem; margin-top:0.25rem; margin-bottom:0;">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Kata Sandi -->
        <div style="margin-bottom:1.25rem;">
            <label style="display:block; font-size:0.75rem; font-weight:700; color:#374151; margin-bottom:0.35rem; text-transform:uppercase; letter-spacing:0.04em;">Kata Sandi</label>
            <div style="position:relative;">
                <i class="bi bi-lock" style="position:absolute; left:0.85rem; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:0.85rem; pointer-events:none;"></i>
                <input 
                    type="password" 
                    wire:model="mahasiswaPassword" 
                    placeholder="••••••••"
                    style="width:100%; padding:0.65rem 0.85rem 0.65rem 2.3rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.86rem; color:#111827; font-family:'Inter',sans-serif; outline:none; transition:all 0.15s;"
                    onfocus="this.style.borderColor='#4f46e5'; this.style.boxShadow='0 0 0 3px rgba(79,70,229,0.1)';"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                >
            </div>
            @error('mahasiswaPassword') <p style="color:#dc2626; font-size:0.76rem; margin-top:0.25rem; margin-bottom:0;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p> @enderror
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            wire:loading.attr="disabled"
            style="width:100%; background:#4f46e5; color:#fff; border:none; border-radius:8px; padding:0.75rem; font-size:0.88rem; font-weight:700; font-family:'Inter',sans-serif; cursor:pointer; transition:background 0.15s; display:flex; align-items:center; justify-content:center; gap:0.5rem; box-shadow:0 4px 12px rgba(79,70,229,0.25);"
            onmouseover="this.style.background='#4338ca';"
            onmouseout="this.style.background='#4f46e5';"
        >
            <span wire:loading.remove wire:target="loginMahasiswa"><i class="bi bi-box-arrow-in-right me-1"></i> Masuk Ujian Mahasiswa</span>
            <span wire:loading wire:target="loginMahasiswa"><span class="spinner-border spinner-border-sm" role="status"></span> Memproses...</span>
        </button>
    </form>

    <!-- Demo accounts Mahasiswa -->
    <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:0.75rem 0.9rem; margin-top:1.25rem; text-align:center;">
        <p style="font-size:0.72rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.4rem;">Contoh Login Mahasiswa</p>
        <div style="font-size:0.76rem; color:#4b5563;">
            Cukup masukkan <strong>Nama</strong>, <strong>NIM</strong> (contoh: <code>210101001</code>), pilih <strong>Prodi & Semester</strong>, password: <code>password</code>
        </div>
    </div>

    <!-- 👨‍🏫 FORM LOGIN DOSEN / ADMIN -->
    @else
    <form wire:submit.prevent="loginAdmin">
        <!-- Email -->
        <div style="margin-bottom:1rem;">
            <label style="display:block; font-size:0.75rem; font-weight:700; color:#374151; margin-bottom:0.35rem; text-transform:uppercase; letter-spacing:0.04em;">Email Dosen / Admin</label>
            <div style="position:relative;">
                <i class="bi bi-envelope" style="position:absolute; left:0.85rem; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:0.85rem; pointer-events:none;"></i>
                <input 
                    type="email" 
                    wire:model="email" 
                    placeholder="dosen@kpi.com / admin@kpi.com"
                    autocomplete="email"
                    style="width:100%; padding:0.68rem 0.85rem 0.68rem 2.3rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.86rem; color:#111827; font-family:'Inter',sans-serif; outline:none; transition:all 0.15s;"
                    onfocus="this.style.borderColor='#4f46e5'; this.style.boxShadow='0 0 0 3px rgba(79,70,229,0.1)';"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                >
            </div>
            @error('email') <p style="color:#dc2626; font-size:0.76rem; margin-top:0.25rem; margin-bottom:0;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p> @enderror
        </div>

        <!-- Password -->
        <div style="margin-bottom:1.5rem;">
            <label style="display:block; font-size:0.75rem; font-weight:700; color:#374151; margin-bottom:0.35rem; text-transform:uppercase; letter-spacing:0.04em;">Kata Sandi Admin</label>
            <div style="position:relative;">
                <i class="bi bi-lock" style="position:absolute; left:0.85rem; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:0.85rem; pointer-events:none;"></i>
                <input 
                    type="password" 
                    wire:model="password" 
                    placeholder="••••••••"
                    autocomplete="current-password"
                    style="width:100%; padding:0.68rem 0.85rem 0.68rem 2.3rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.86rem; color:#111827; font-family:'Inter',sans-serif; outline:none; transition:all 0.15s;"
                    onfocus="this.style.borderColor='#4f46e5'; this.style.boxShadow='0 0 0 3px rgba(79,70,229,0.1)';"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                >
            </div>
            @error('password') <p style="color:#dc2626; font-size:0.76rem; margin-top:0.25rem; margin-bottom:0;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p> @enderror
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            wire:loading.attr="disabled"
            style="width:100%; background:#1e1b4b; color:#fff; border:none; border-radius:8px; padding:0.75rem; font-size:0.88rem; font-weight:700; font-family:'Inter',sans-serif; cursor:pointer; transition:background 0.15s; display:flex; align-items:center; justify-content:center; gap:0.5rem; box-shadow:0 4px 12px rgba(30,27,75,0.25);"
            onmouseover="this.style.background='#312e81';"
            onmouseout="this.style.background='#1e1b4b';"
        >
            <span wire:loading.remove wire:target="loginAdmin"><i class="bi bi-shield-lock me-1"></i> Masuk Sebagai Dosen / Admin</span>
            <span wire:loading wire:target="loginAdmin"><span class="spinner-border spinner-border-sm" role="status"></span> Memproses...</span>
        </button>
    </form>

    <!-- Demo accounts Admin -->
    <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:0.75rem 0.9rem; margin-top:1.25rem; text-align:center;">
        <p style="font-size:0.72rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.4rem;">Akun Dosen & Admin</p>
        <div style="display:flex; flex-wrap:wrap; gap:0.35rem; justify-content:center; margin-bottom:0.4rem;">
            <span style="font-size:0.72rem; padding:0.18rem 0.5rem; background:#eff6ff; color:#2563eb; border:1px solid #dbeafe; border-radius:5px; font-weight:600;">
                <i class="bi bi-person-badge me-1"></i>dosen@kpi.com
            </span>
            <span style="font-size:0.72rem; padding:0.18rem 0.5rem; background:#fef2f2; color:#dc2626; border:1px solid #fecaca; border-radius:5px; font-weight:600;">
                <i class="bi bi-shield-fill me-1"></i>admin@kpi.com
            </span>
        </div>
        <p style="font-size:0.75rem; color:#9ca3af; margin:0;">Password: <code style="background:#e5e7eb; padding:0.1rem 0.35rem; border-radius:4px; font-size:0.75rem; color:#374151;">password</code></p>
    </div>
    @endif

</div>
