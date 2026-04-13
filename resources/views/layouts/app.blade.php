<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Latihan UTBK 2025' }}</title>
    
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @livewireStyles
    <style>
        /* Smooth Dark Mode Overrides */
        body { transition: background-color 0.3s ease, color 0.3s ease; }
        .theme-dark { background-color: #1b1b29 !important; color: #ced4da !important; }
        .theme-dark #main { background-color: #1b1b29 !important; }
        .theme-dark .card { background-color: #252538 !important; border: 1px solid #2d2d44 !important; color: #e9ecef !important; }
        .theme-dark .card-header, .theme-dark .card-footer { background-color: transparent !important; border-color: #2d2d44 !important; }
        .theme-dark .sidebar-wrapper { background-color: #1e1e2d !important; border-right: 1px solid #2d2d44; }
        .theme-dark header, .theme-dark footer { background-color: #1e1e2d !important; border-color: #2d2d44 !important; color: #ced4da !important; }
        .theme-dark .table { color: #ced4da !important; border-color: #2d2d44 !important; }
        .theme-dark .table thead th { background-color: #252538 !important; color: #9a9abb !important; border-color: #2d2d44 !important; }
        .theme-dark .text-muted { color: #8a8a9a !important; }
        .theme-dark .bg-light { background-color: #2d2d44 !important; color: #ced4da !important; }
        .theme-dark .input-group-text, .theme-dark .form-control { background-color: #2d2d44 !important; border-color: #3f3f5a !important; color: #fff !important; }
        .theme-dark .sidebar-link:hover { background-color: #2d2d44 !important; }
        .theme-dark .sidebar-item.active > .sidebar-link { background-color: #435ebe !important; }
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
                    <div class="d-flex justify-content-between">
                        <div class="logo">
                            <h3 class="fw-bold text-primary">UTBK <span class="text-secondary">2025</span></h3>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title mt-4 mb-2 opacity-50 text-uppercase small">Menu</li>

                        <li class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">
                            <a href="/dashboard" class='sidebar-link' @if(request()->is('dashboard')) @else wire:navigate @endif>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->is('tryouts*') ? 'active' : '' }}">
                            <a href="/tryouts" class='sidebar-link' @if(request()->is('tryouts*')) @else wire:navigate @endif>
                                <i class="bi bi-journal-check"></i>
                                <span>My Tryouts</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->is('leaderboard') ? 'active' : '' }}">
                            <a href="/leaderboard" class='sidebar-link' @if(request()->is('leaderboard')) @else wire:navigate @endif>
                                <i class="bi bi-trophy-fill"></i>
                                <span>Leaderboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->is('rationalization') ? 'active' : '' }}">
                            <a href="/rationalization" class='sidebar-link' @if(request()->is('rationalization')) @else wire:navigate @endif>
                                <i class="bi bi-graph-up-arrow"></i>
                                <span>Rasionalisasi</span>
                            </a>
                        </li>

                        @if(auth()->check() && auth()->user()->is_admin)
                        <li class="sidebar-title mt-4 mb-2 opacity-50 text-uppercase small">Admin</li>
                        <li class="sidebar-item {{ request()->is('admin/exams') ? 'active' : '' }}">
                            <a href="/admin/exams" class='sidebar-link' wire:navigate>
                                <i class="bi bi-stack"></i>
                                <span>Management Tryout</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->is('admin/generator') ? 'active' : '' }}">
                            <a href="/admin/generator" class='sidebar-link' wire:navigate>
                                <i class="bi bi-plus-circle-fill"></i>
                                <span>Question Generator</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->is('admin/users') ? 'active' : '' }}">
                            <a href="/admin/users" class='sidebar-link' wire:navigate>
                                <i class="bi bi-people-fill"></i>
                                <span>Monitor Peserta</span>
                            </a>
                        </li>
                        @endif

                        <li class="sidebar-item mt-4">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="sidebar-link border-0 bg-transparent w-100 text-start">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Logout</span>
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
                        <div class="theme-toggle d-flex gap-2 align-items-center me-4 pointer" @click="toggleDark()" style="cursor: pointer;">
                            <i class="bi bi-sun-fill fs-5 text-warning" x-show="!darkMode"></i>
                            <i class="bi bi-moon-stars-fill fs-5 text-primary" x-show="darkMode"></i>
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer" :checked="darkMode">
                                <label class="form-check-label"></label>
                            </div>
                        </div>

                        <a href="/profile" class="text-end me-3 text-decoration-none" wire:navigate>
                            <h6 class="mb-0" :class="darkMode ? 'text-white' : 'text-gray-600'">{{ Auth::user()->name }}</h6>
                            <p class="mb-0 text-sm text-muted small">{{ Auth::user()->school }}</p>
                        </a>
                        <a href="/profile" class="avatar avatar-md border p-1 rounded-circle" wire:navigate>
                            <i class="bi bi-person-fill fs-4 px-2"></i>
                        </a>
                    <style>
        :root {
            --bg-dark-card: #252538;
            --bg-dark-body: #1e1e2d;
            --text-muted: #94a3b8;
        }
        
        body.theme-dark {
            background-color: var(--bg-dark-body) !important;
        }

        .theme-dark .card {
            background-color: var(--bg-dark-card) !important;
            border: none !important;
        }

        /* Fix Table Row Putih yang Silau */
        .theme-dark .table {
            color: #cbd5e1 !important;
        }
        .theme-dark .table thead tr {
            background-color: rgba(255,255,255,0.05) !important;
            color: #fff !important;
        }
        .theme-dark .table tbody tr:hover {
            background-color: rgba(255,255,255,0.03) !important;
        }
        .theme-dark .table td, .theme-dark .table th {
            border-color: rgba(255,255,255,0.05) !important;
            background-color: transparent !important;
            color: #cbd5e1 !important;
        }

        /* Fix Stats Icon */
        .theme-dark .stats-icon {
            background-color: rgba(255,255,255,0.1) !important;
        }

        /* Fix Overlapping Shield in Rationalization */
        .shield-icon-container {
            z-index: 1;
            opacity: 0.1;
        }

        /* Smooth Transition */
        * { transition: background-color 0.3s ease, border-color 0.3s ease; }
    </style>
                    </div>
                </div>
            </header>
            
            <div id="main-content">
                <div class="container-fluid">
                    {{ $slot }}
                </div>
            </div>
            
            <footer class="mt-5 p-4 border-top" :class="darkMode ? '' : 'bg-white'">
                <div class="footer clearfix mb-0 text-muted container-fluid">
                    <div class="float-start">
                        <p>2026 &copy; Platform Edukasi UTBK</p>
                    </div>
                    <div class="float-end">
                        <p>Developed with <span class="text-danger"><i class="bi bi-heart"></i></span> by <span class="text-primary fw-bold">Fajar Pranayoga</span></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @livewireScripts
</body>
</html>
