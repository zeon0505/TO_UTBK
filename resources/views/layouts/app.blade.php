<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Portal Ujian Tengah Semester (UTS)' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --bg-dark-card: #252538;
            --bg-dark-body: #1e1e2d;
            --text-muted: #94a3b8;
        }
        body { transition: background-color 0.3s ease, color 0.3s ease; }
        .theme-dark { background-color: var(--bg-dark-body) !important; color: #ced4da !important; }
        .theme-dark #main { background-color: var(--bg-dark-body) !important; }
        .theme-dark .card { background-color: var(--bg-dark-card) !important; border: 1px solid #2d2d44 !important; color: #e9ecef !important; }
        .theme-dark .card-header, .theme-dark .card-footer { background-color: transparent !important; border-color: #2d2d44 !important; }
        .theme-dark .sidebar-wrapper { background-color: #1e1e2d !important; border-right: 1px solid #2d2d44; }
        .theme-dark header, .theme-dark footer { background-color: #1e1e2d !important; border-color: #2d2d44 !important; color: #ced4da !important; }
        .theme-dark .table { color: #ced4da !important; border-color: #2d2d44 !important; }
        .theme-dark .table thead th { background-color: #252538 !important; color: #9a9abb !important; border-color: #2d2d44 !important; }
        .theme-dark .table td, .theme-dark .table th { border-color: rgba(255,255,255,0.05) !important; background-color: transparent !important; color: #cbd5e1 !important; }
        .theme-dark .text-muted { color: #8a8a9a !important; }
        .theme-dark .bg-light { background-color: #2d2d44 !important; color: #ced4da !important; }
        .theme-dark .input-group-text, .theme-dark .form-control, .theme-dark .form-select { background-color: #2d2d44 !important; border-color: #3f3f5a !important; color: #fff !important; }
        .sidebar-wrapper {
            height: 100vh !important;
            max-height: 100vh !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }
        .sidebar-wrapper::-webkit-scrollbar { width: 5px; }
        .sidebar-wrapper::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 4px; }
        .theme-dark .sidebar-wrapper::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); }
        .rotate-180 { transform: rotate(180deg); }

        /* Semester Submenu styling for maximum legibility */
        .semester-sub-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 6px;
            margin-top: 4px;
            border: 1px solid #e2e8f0;
        }
        .theme-dark .semester-sub-box {
            background: #252538;
            border-color: #3f3f5a;
        }
        .semester-link-item {
            color: #1e293b !important;
            font-weight: 600 !important;
            transition: all 0.15s ease;
        }
        .semester-link-item:hover {
            background: #e2e8f0 !important;
            color: #435ebe !important;
        }
        .theme-dark .semester-link-item {
            color: #e2e8f0 !important;
        }
        .theme-dark .semester-link-item:hover {
            background: #3f3f5a !important;
            color: #ffffff !important;
        }
        .semester-link-item.active-sem {
            background: #435ebe !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            box-shadow: 0 2px 8px rgba(67,94,190,0.35);
        }
    </style>
</head>
<body x-data="{ 
    darkMode: localStorage.getItem('theme') === 'dark',
    toggleDark() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
    }
}" :class="darkMode ? 'theme-dark' : ''">
    <div id="app" class="{{ request()->routeIs('exam.show') ? 'sidebar-hidden' : '' }}">
        <div id="sidebar" class="active" @if(request()->routeIs('exam.show')) style="display: none !important;" @endif>
            <div class="sidebar-wrapper active">
                <div class="sidebar-header p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <h4 class="fw-bold text-primary mb-0"><i class="bi bi-mortarboard-fill me-2"></i>UTS <span class="text-secondary fs-6">KAMPUS</span></h4>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title mt-2 mb-2 opacity-50 text-uppercase small">Menu Utama</li>

                        <li class="sidebar-item {{ request()->is('dashboard') && !request('semester') ? 'active' : '' }}">
                            <a href="/dashboard" class='sidebar-link' wire:navigate>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard Utama</span>
                            </a>
                        </li>

                        <!-- Collapsible Kategori Semester Dropdown -->
                        <li class="sidebar-item" x-data="{ open: {{ request()->has('semester') ? 'true' : 'false' }} }">
                            <a href="#" @click.prevent="open = !open" class='sidebar-link d-flex align-items-center justify-content-between me-2'>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-journal-bookmark-fill text-primary"></i>
                                    <span>Kategori Semester</span>
                                </div>
                                <i class="bi bi-chevron-down small" :class="open ? 'rotate-180' : ''" style="transition: transform 0.2s ease;"></i>
                            </a>
                            <div class="semester-sub-box mx-2" x-show="open" x-collapse style="display: none;">
                                <ul style="list-style: none; padding: 0; margin: 0;">
                                    @for($sem = 1; $sem <= 8; $sem++)
                                    <li class="my-1">
                                        <a href="/dashboard?semester={{ $sem }}" 
                                           class="sidebar-link semester-link-item py-2 px-3 rounded-3 text-decoration-none small d-flex align-items-center justify-content-between {{ request('semester') == $sem ? 'active-sem' : '' }}" 
                                           wire:navigate>
                                            <span><i class="bi bi-bookmark-star-fill me-2 text-primary opacity-75"></i>Semester {{ $sem }}</span>
                                            @if(request('semester') == $sem)
                                                <i class="bi bi-check-circle-fill fs-6 text-white"></i>
                                            @endif
                                        </a>
                                    </li>
                                    @endfor
                                </ul>
                            </div>
                        </li>

                        @if(auth()->check() && (auth()->user()->isDosen() || auth()->user()->isAdmin()))
                        <li class="sidebar-title mt-4 mb-2 opacity-50 text-uppercase small">Manajemen Dosen</li>
                        <li class="sidebar-item {{ request()->is('admin/courses') ? 'active' : '' }}">
                            <a href="/admin/courses" class='sidebar-link' wire:navigate>
                                <i class="bi bi-journal-bookmark-fill"></i>
                                <span>Mata Kuliah</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->is('admin/exams') ? 'active' : '' }}">
                            <a href="/admin/exams" class='sidebar-link' wire:navigate>
                                <i class="bi bi-calendar-event-fill"></i>
                                <span>Jadwal UTS</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->is('admin/generator') ? 'active' : '' }}">
                            <a href="/admin/generator" class='sidebar-link' wire:navigate>
                                <i class="bi bi-file-earmark-plus-fill"></i>
                                <span>Input & Generator Soal</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->is('admin/users') ? 'active' : '' }}">
                            <a href="/admin/users" class='sidebar-link' wire:navigate>
                                <i class="bi bi-people-fill"></i>
                                <span>Kelola Pengguna</span>
                            </a>
                        </li>
                        @endif

                        <li class="sidebar-title mt-4 mb-2 opacity-50 text-uppercase small">Pengaturan</li>
                        <li class="sidebar-item {{ request()->is('profile') ? 'active' : '' }}">
                            <a href="/profile" class='sidebar-link' wire:navigate>
                                <i class="bi bi-person-circle"></i>
                                <span>Profil Saya</span>
                            </a>
                        </li>

                        <li class="sidebar-item mt-4">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="sidebar-link border-0 bg-transparent w-100 text-start text-danger">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Keluar (Logout)</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div id="main">
            <header class='mb-4 shadow-sm p-3' :class="darkMode ? '' : 'bg-white'">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <a href="#" class="burger-btn d-block d-xl-none">
                        <i class="bi bi-justify fs-3"></i>
                    </a>
                    <div class="user-info ms-auto d-flex align-items-center">
                        <!-- Dark Mode Toggle -->
                        <div class="theme-toggle d-flex gap-2 align-items-center me-4 pointer" @click="toggleDark()">
                            <i class="bi bi-sun-fill fs-5 text-warning" x-show="!darkMode"></i>
                            <i class="bi bi-moon-stars-fill fs-5 text-primary" x-show="darkMode"></i>
                            <div class="form-check form-switch fs-6 mb-0">
                                <input class="form-check-input me-0 pointer" type="checkbox" id="toggle-dark" :checked="darkMode">
                            </div>
                        </div>

                        @auth
                        <a href="/profile" class="text-end me-3 text-decoration-none" wire:navigate>
                            <h6 class="mb-0 fw-semibold" :class="darkMode ? 'text-white' : 'text-dark'">{{ Auth::user()->name }}</h6>
                            <small class="text-muted">
                                @if(Auth::user()->role === 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @elseif(Auth::user()->role === 'dosen')
                                    <span class="badge bg-primary">Dosen (NIP: {{ Auth::user()->nip ?? '-' }})</span>
                                @else
                                    <span class="badge bg-info text-dark">Mahasiswa (NIM: {{ Auth::user()->nim ?? '-' }})</span>
                                @endif
                            </small>
                        </a>
                        <a href="/profile" class="avatar avatar-md border p-1 rounded-circle bg-primary text-white text-center" wire:navigate style="width:40px; height:40px; display:inline-flex; align-items:center; justify-content:center;">
                            <i class="bi bi-person-fill fs-4"></i>
                        </a>
                        @endauth
                    </div>
                </div>
            </header>
            
            <div id="main-content" class="{{ session('just_logged_in') ? 'login-entrance' : '' }}">
                <div class="container-fluid">
                    {{ $slot }}
                </div>
            </div>
            
            <footer class="mt-5 p-4 border-top" :class="darkMode ? '' : 'bg-white'">
                <div class="footer clearfix mb-0 text-muted container-fluid">
                    <div class="float-start">
                        <p class="mb-0">2026 &copy; Portal Ujian Tengah Semester (UTS) Kampus</p>
                    </div>
                    <div class="float-end">
                        <p class="mb-0">Sistem Evaluasi Akademik Perkuliahan</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @livewireScripts
    @if(session('just_logged_in'))
    <script>
        // Remove the just_logged_in session flag after animation plays
        fetch('/clear-login-flag', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' } }).catch(() => {});
    </script>
    @endif
</body>
</html>
