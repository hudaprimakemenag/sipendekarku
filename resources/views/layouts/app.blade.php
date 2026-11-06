<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIPENDEKARKU — Sistem Informasi Pendidikan Karakter Anak Kulon Progo' }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ route('sipendekarku.styles') }}">
    
    <style>
        /* Additional Custom Utilities for CRUD & Reports */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 16px;
            font-size: 13px;
            font-weight: 700;
            border-radius: var(--radius-md);
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        .btn-primary {
            background: var(--emerald);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(5, 150, 105, 0.25);
        }
        .btn-primary:hover {
            background: var(--emerald-dark);
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: #ffffff;
            color: var(--slate-700);
            border-color: var(--slate-200);
        }
        .btn-secondary:hover {
            background: var(--slate-50);
            border-color: var(--slate-300);
        }
        .btn-danger {
            background: var(--rose);
            color: #ffffff;
        }
        .btn-danger:hover {
            background: #be123c;
        }
        .btn-amber {
            background: var(--amber);
            color: #ffffff;
        }
        .btn-amber:hover {
            background: var(--amber-dark);
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: var(--radius-sm);
        }

        /* Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: var(--radius-lg);
            border: 1px solid var(--line);
            background: #ffffff;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }
        .data-table th {
            background: var(--slate-50);
            color: var(--slate-600);
            font-weight: 700;
            padding: 12px 16px;
            border-bottom: 1px solid var(--line);
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
        }
        .data-table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            color: var(--dark);
            vertical-align: middle;
        }
        .data-table tbody tr:last-child td {
            border-bottom: none;
        }
        .data-table tbody tr:hover {
            background: #f8fafc;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: var(--radius-full);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .badge-success {
            background: var(--emerald-light);
            color: var(--emerald-dark);
            border: 1px solid var(--emerald-border);
        }
        .badge-warning {
            background: var(--amber-light);
            color: var(--amber-dark);
            border: 1px solid var(--amber-border);
        }
        .badge-danger {
            background: var(--rose-light);
            color: var(--rose);
            border: 1px solid var(--rose-border);
        }
        .badge-neutral {
            background: var(--slate-100);
            color: var(--slate-600);
            border: 1px solid var(--slate-200);
        }

        /* Modals */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-dialog {
            background: #ffffff;
            border-radius: var(--radius-xl);
            width: 100%;
            max-width: 540px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: modalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes modalPop {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--dark);
        }
        .modal-body {
            padding: 24px;
            max-height: 75vh;
            overflow-y: auto;
        }
        .modal-footer {
            padding: 16px 24px;
            background: var(--slate-50);
            border-top: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--slate-700);
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border-radius: var(--radius-md);
            border: 1px solid var(--slate-200);
            font-size: 13px;
            font-family: inherit;
            color: var(--dark);
            background: #ffffff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--emerald);
            box-shadow: 0 0 0 3px var(--emerald-focus);
        }
        .form-hint {
            font-size: 11px;
            color: var(--slate-400);
            margin-top: 4px;
        }
        .form-error {
            font-size: 11px;
            color: var(--rose);
            margin-top: 4px;
            font-weight: 600;
        }

        /* Card Container */
        .panel {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
        }
        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .panel-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--dark);
        }

        /* Flash Message */
        .alert-box {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success {
            background: var(--emerald-light);
            border: 1px solid var(--emerald-border);
            color: var(--emerald-dark);
        }
        .alert-danger {
            background: var(--rose-light);
            border: 1px solid var(--rose-border);
            color: var(--rose);
        }

        /* User Dropdown */
        .user-dropdown-item:hover {
            background: var(--slate-100, #f1f5f9) !important;
            color: var(--dark) !important;
        }
        .user-dropdown-logout:hover {
            background: #ffe4e6 !important;
            color: #e11d48 !important;
        }
    </style>
    @livewireStyles
</head>
<body>
    @php
        $user = auth()->user();
        $role = $user?->primaryRole() ?? 'student';
        $school = $user?->school;
        $academicYear = $school?->activeAcademicYear;
        $semester = $academicYear?->activeSemester;
    @endphp

    <div class="app-shell" x-data="{ menuOpen: false }">
        <!-- Backdrop -->
        <div class="sidebar-backdrop" :class="{ 'active': menuOpen }" @click="menuOpen = false"></div>

        <!-- Sidebar -->
        <aside class="sidebar" :class="{ 'open': menuOpen }">
            <div class="sidebar-header">
                <a class="brand" href="{{ route('dashboard') }}" style="gap: 12px; align-items: center;">
                    <img src="{{ asset('images/logokemenag.png') }}" alt="Logo Kemenag" style="width: 36px; height: 36px; flex-shrink: 0; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
                    <span style="display: flex; flex-direction: column; line-height: 1.2;">
                        <b style="font-size: 17px; font-weight: 800; letter-spacing: 0.3px; color: var(--dark);">SIPENDEKARKU</b>
                        <small style="font-size: 9px; font-weight: 600; color: var(--emerald-dark); letter-spacing: 0.2px; line-height: 1.1;">Pendidikan Karakter Kulon Progo</small>
                    </span>
                </a>
                <button type="button" class="sidebar-close-btn" @click="menuOpen = false" aria-label="Tutup menu">✕</button>
            </div>


            <!-- School / Scope Info -->
            <div class="school">
                <span class="school-icon">⌂</span>
                <div>
                    <b>{{ $school?->name ?? 'Pusat SIPENDEKARKU' }}</b>
                    <small>{{ $academicYear?->name ?? '2026/2027' }} · {{ $semester?->name ?? 'Ganjil' }}</small>
                </div>
            </div>

            <!-- Role-based Navigation -->
            <nav class="nav" aria-label="Navigasi utama">
                @if ($role === 'student')
                    <a class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                        <span>▦</span> Mutaba'ah Hari Ini
                    </a>
                    <a class="{{ request()->routeIs('student.history') ? 'active' : '' }}" href="{{ route('student.history') }}">
                        <span>◴</span> Riwayat Kegiatan
                    </a>
                    <a class="{{ request()->routeIs('student.leaderboard') ? 'active' : '' }}" href="{{ route('student.leaderboard') }}">
                        <span>✦</span> Poin & Ranking
                    </a>
                @elseif ($role === 'teacher' || $role === 'school_admin')
                    <a class="{{ request()->routeIs('teacher.dashboard', 'admin.dashboard') ? 'active' : '' }}" href="{{ route('teacher.dashboard') }}">
                        <span>▦</span> Beranda Guru
                    </a>
                    <a class="{{ request()->routeIs('teacher.verification') ? 'active' : '' }}" href="{{ route('teacher.verification') }}">
                        <span>✓</span> Verifikasi Kegiatan
                    </a>
                    <a class="{{ request()->routeIs('teacher.reports') ? 'active' : '' }}" href="{{ route('teacher.reports') }}">
                        <span>▤</span> Laporan Kelas
                    </a>

                    <div style="padding: 12px 14px 4px 14px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--slate-400);">
                        Manajemen Sekolah
                    </div>

                    <a class="{{ request()->routeIs('teacher.classes', 'admin.classes') ? 'active' : '' }}" href="{{ route('teacher.classes') }}">
                        <span>⌂</span> Kelola Kelas
                    </a>
                    <a class="{{ request()->routeIs('teacher.academic-years', 'admin.academic-years') ? 'active' : '' }}" href="{{ route('teacher.academic-years') }}">
                        <span>◷</span> Tahun & Semester
                    </a>
                    <a class="{{ request()->routeIs('teacher.teachers', 'admin.teachers') ? 'active' : '' }}" href="{{ route('teacher.teachers') }}">
                        <span>♟</span> Data & Guru Pengampu
                    </a>
                    <a class="{{ request()->routeIs('teacher.students', 'admin.students') ? 'active' : '' }}" href="{{ route('teacher.students') }}">
                        <span>🤼</span> Data & Penempatan Siswa
                    </a>
                    <a class="{{ request()->routeIs('teacher.parents', 'admin.parents') ? 'active' : '' }}" href="{{ route('teacher.parents') }}">
                        <span>👪</span> Wali Murid & Relasi
                    </a>
                    <a class="{{ request()->routeIs('teacher.school-reports', 'admin.reports') ? 'active' : '' }}" href="{{ route('teacher.school-reports') }}">
                        <span>▤</span> Laporan & Ranking Sekolah
                    </a>
                @elseif ($role === 'parent')
                    <a class="{{ request()->routeIs('parent.dashboard') ? 'active' : '' }}" href="{{ route('parent.dashboard') }}">
                        <span>▦</span> Pemantauan Anak
                    </a>
                @elseif ($role === 'superadmin')
                    <a class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}" href="{{ route('superadmin.dashboard') }}">
                        <span>▦</span> Dashboard Global
                    </a>
                    <a class="{{ request()->routeIs('superadmin.schools') ? 'active' : '' }}" href="{{ route('superadmin.schools') }}">
                        <span>🏢</span> Kelola Sekolah
                    </a>
                    <a class="{{ request()->routeIs('superadmin.users') ? 'active' : '' }}" href="{{ route('superadmin.users') }}">
                        <span>👥</span> Manajemen Pengguna
                    </a>
                    <a class="{{ request()->routeIs('superadmin.catalog') ? 'active' : '' }}" href="{{ route('superadmin.catalog') }}">
                        <span>📋</span> Katalog Kegiatan
                    </a>
                    <a class="{{ request()->routeIs('superadmin.reports') ? 'active' : '' }}" href="{{ route('superadmin.reports') }}">
                        <span>▤</span> Laporan Lintas Sekolah
                    </a>
                @endif
            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <div class="user-avatar">{{ strtoupper(substr($user?->name ?? 'US', 0, 2)) }}</div>
                <div>
                    <b>{{ $user?->name ?? 'Pengguna' }}</b>
                    <small>
                        {{ match($role) {
                            'superadmin' => 'Superadmin',
                            'school_admin' => 'Admin Sekolah',
                            'teacher' => 'Guru Pengampu',
                            'parent' => 'Wali Murid',
                            default => 'Siswa',
                        } }}
                    </small>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                    @csrf
                    <button type="submit" aria-label="Keluar" title="Keluar" style="cursor: pointer; border: 0; background: transparent; color: var(--slate-400); font-size: 18px;">↗</button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main>
            <header class="topbar">
                <button class="menu-btn" @click="menuOpen = !menuOpen" aria-label="Buka menu">☰</button>
                <div class="breadcrumb">
                    <span>{{ $breadcrumbSection ?? 'SIPENDEKARKU' }}</span>
                    <strong> / </strong>
                    <b>{{ $breadcrumbTitle ?? 'Dashboard' }}</b>
                </div>
                <div class="top-actions">
                    <span class="badge badge-success" style="padding: 6px 12px;">
                        {{ match($role) {
                            'superadmin' => 'Superadmin Global',
                            'school_admin' => 'Admin Sekolah',
                            'teacher' => 'Guru Pengampu',
                            'parent' => 'Wali Murid',
                            default => 'Siswa Aktif',
                        } }}
                    </span>
                    {{-- Avatar Singkatan Nama dengan Dropdown Menu (Hover & Klik) --}}
                    <div class="user-menu-dropdown-wrap" 
                         x-data="{ open: false }" 
                         @mouseenter="open = true" 
                         @mouseleave="open = false" 
                         @click.outside="open = false" 
                         style="position: relative; display: inline-block;">

                        <button type="button" 
                                @click="open = !open" 
                                class="avatar-trigger-btn" 
                                aria-haspopup="true" 
                                :aria-expanded="open" 
                                title="Menu Pengguna: {{ $user?->name ?? 'Pengguna' }}" 
                                style="background: transparent; border: none; padding: 0; cursor: pointer; display: flex; align-items: center; gap: 6px; border-radius: var(--radius-full); outline: none;">
                            <div class="avatar" 
                                 style="transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); user-select: none; cursor: pointer;" 
                                 :style="open ? 'box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.35); transform: scale(1.05);' : ''">
                                {{ strtoupper(substr($user?->name ?? 'US', 0, 2)) }}
                            </div>
                            <span style="font-size: 10px; color: var(--slate-400); transition: transform 0.2s ease;" :style="open ? 'transform: rotate(180deg);' : ''">▾</span>
                        </button>

                        {{-- Dropdown Popup --}}
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 transform scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 transform scale-95 -translate-y-1"
                             class="user-dropdown-menu"
                             style="position: absolute; right: 0; top: calc(100% + 6px); width: 250px; background: #ffffff; border: 1px solid var(--line); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); z-index: 60; overflow: hidden;"
                             x-cloak>
                            
                            {{-- Info Akun --}}
                            <div style="padding: 12px 16px; border-bottom: 1px solid var(--line); background: var(--slate-50, #f8fafc);">
                                <div style="font-size: 13px; font-weight: 700; color: var(--dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $user?->name ?? 'Pengguna' }}
                                </div>
                                <div style="font-size: 11px; color: var(--slate-500); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $user?->email }}
                                </div>
                                <div style="margin-top: 8px;">
                                    <span class="badge badge-success" style="font-size: 10px; padding: 2px 8px;">
                                        {{ match($role) {
                                            'superadmin' => 'Superadmin Global',
                                            'school_admin' => 'Admin Sekolah',
                                            'teacher' => 'Guru Pengampu',
                                            'parent' => 'Wali Murid',
                                            default => 'Siswa Aktif',
                                        } }}
                                    </span>
                                </div>
                            </div>

                            {{-- Menu Aksi --}}
                            <div style="padding: 6px;">
                                <button type="button" 
                                        @click="open = false; $dispatch('open-change-password')" 
                                        class="user-dropdown-item" 
                                        style="width: 100%; display: flex; align-items: center; gap: 10px; padding: 10px 12px; font-size: 13px; font-weight: 600; color: var(--slate-700); background: transparent; border: none; border-radius: var(--radius-md); cursor: pointer; text-align: left; transition: all 0.15s ease;">
                                    <span style="font-size: 16px;">🔑</span>
                                    <span>Ubah Kata Sandi</span>
                                </button>

                                <div style="height: 1px; background: var(--line); margin: 4px 6px;"></div>

                                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                                    @csrf
                                    <button type="submit" 
                                            class="user-dropdown-item user-dropdown-logout" 
                                            style="width: 100%; display: flex; align-items: center; gap: 10px; padding: 10px 12px; font-size: 13px; font-weight: 600; color: var(--rose, #e11d48); background: transparent; border: none; border-radius: var(--radius-md); cursor: pointer; text-align: left; transition: all 0.15s ease;">
                                        <span style="font-size: 16px;">🚪</span>
                                        <span>Keluar (Logout)</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="page">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- Modal Ubah Kata Sandi (Render di root body, di luar header agar tidak terisolasi backdrop-filter header) -->
    <livewire:auth.change-password />

    @livewireScripts
</body>
</html>
