<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">{{ $school?->name ?? 'Sekolah' }} · Admin Panel</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Dashboard Administrator Sekolah</h1>
            <p style="color: var(--slate-500); font-size: 13px;">
                Kelola struktur tahun ajaran, semester, kelas, guru pengampu, siswa, wali murid, dan laporan monitoring.
            </p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.students') }}" class="btn btn-secondary">
                + Tambah Siswa
            </a>
            <a href="{{ route('admin.classes') }}" class="btn btn-primary">
                + Kelola Kelas
            </a>
        </div>
    </div>

    <!-- Metrics Overview -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <article class="metric points">
            <div class="metric-icon" style="background:#dbeafe; color:#1d4ed8;">🤼</div>
            <div>
                <span>Total Siswa</span>
                <strong>{{ $totalStudents }}</strong>
                <small>{{ $activeStudentsToday }} aktif hari ini ({{ $activePercent }}%)</small>
            </div>
        </article>

        <article class="metric points">
            <div class="metric-icon" style="background:#e0e7ff; color:#4338ca;">♟</div>
            <div>
                <span>Total Guru</span>
                <strong>{{ $totalTeachers }}</strong>
                <small>Pengampu kelas</small>
            </div>
        </article>

        <article class="metric points">
            <div class="metric-icon" style="background:#fef3c7; color:#b45309;">👪</div>
            <div>
                <span>Wali Murid</span>
                <strong>{{ $totalParents }}</strong>
                <small>Terdaftar di sistem</small>
            </div>
        </article>

        <article class="metric rank">
            <div class="metric-icon">⌂</div>
            <div>
                <span>Kelas Aktif</span>
                <strong>{{ $totalClasses }}</strong>
                <small>Tahun Ajaran {{ $academicYear?->name ?? '2026/2027' }}</small>
            </div>
        </article>
    </div>

    <!-- Quick Navigation Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <a href="{{ route('admin.classes') }}" style="text-decoration: none; color: inherit;">
            <div class="panel" style="padding: 20px; transition: transform 0.2s ease; cursor: pointer; border-left: 4px solid var(--emerald);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 20px;">⌂</span>
                    <span class="badge badge-success">Kelola</span>
                </div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">Kelola Kelas</h3>
                <p style="font-size: 12px; color: var(--slate-500);">Atur kelas, tingkat rombel, dan lihat daftar siswa/guru.</p>
            </div>
        </a>

        <a href="{{ route('admin.academic-years') }}" style="text-decoration: none; color: inherit;">
            <div class="panel" style="padding: 20px; transition: transform 0.2s ease; cursor: pointer; border-left: 4px solid var(--amber);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 20px;">◷</span>
                    <span class="badge badge-warning">Atur TA</span>
                </div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">Tahun & Semester</h3>
                <p style="font-size: 12px; color: var(--slate-500);">Kelola tahun ajaran & semester aktif (Ganjil/Genap).</p>
            </div>
        </a>

        <a href="{{ route('admin.teachers') }}" style="text-decoration: none; color: inherit;">
            <div class="panel" style="padding: 20px; transition: transform 0.2s ease; cursor: pointer; border-left: 4px solid #6366f1;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 20px;">♟</span>
                    <span class="badge badge-neutral">Penugasan</span>
                </div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">Guru & Penugasan</h3>
                <p style="font-size: 12px; color: var(--slate-500);">Tugaskan guru pengampu ke satu atau banyak kelas.</p>
            </div>
        </a>

        <a href="{{ route('admin.students') }}" style="text-decoration: none; color: inherit;">
            <div class="panel" style="padding: 20px; transition: transform 0.2s ease; cursor: pointer; border-left: 4px solid #0284c7;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 20px;">🤼</span>
                    <span class="badge badge-neutral">Penempatan</span>
                </div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">Siswa & Penempatan</h3>
                <p style="font-size: 12px; color: var(--slate-500);">Tempatkan siswa ke kelas per semester dan mutasi.</p>
            </div>
        </a>

        <a href="{{ route('admin.parents') }}" style="text-decoration: none; color: inherit;">
            <div class="panel" style="padding: 20px; transition: transform 0.2s ease; cursor: pointer; border-left: 4px solid #d97706;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 20px;">👪</span>
                    <span class="badge badge-neutral">Relasi</span>
                </div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">Wali Murid & Relasi</h3>
                <p style="font-size: 12px; color: var(--slate-500);">Hubungkan akun wali murid dengan satu atau beberapa siswa.</p>
            </div>
        </a>

        <a href="{{ route('admin.reports') }}" style="text-decoration: none; color: inherit;">
            <div class="panel" style="padding: 20px; transition: transform 0.2s ease; cursor: pointer; border-left: 4px solid #10b981;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 20px;">▤</span>
                    <span class="badge badge-success">Ekspor</span>
                </div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">Laporan & Ranking</h3>
                <p style="font-size: 12px; color: var(--slate-500);">Lihat leaderboard sekolah, rekapitulasi, dan unduh Excel/PDF.</p>
            </div>
        </a>
    </div>

    <!-- Active Classes Summary Table -->
    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Ringkasan Kelas</h2>
                <small style="color: var(--slate-400);">Semester {{ $semester?->name ?? 'Ganjil' }} · {{ $classes->count() }} Kelas Terdaftar</small>
            </div>
            <a href="{{ route('admin.classes') }}" class="btn btn-secondary btn-sm">Lihat Detail Kelas →</a>
        </div>

        <div class="table-responsive" style="border: none;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Jumlah Siswa</th>
                        <th>Guru Pengampu</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classes as $c)
                        @php
                            $studentsCount = $c->classStudents->whereNull('left_at')->count();
                            $teachersList = $c->classTeachers->whereNull('unassigned_at')->pluck('teacher.name')->filter()->join(', ');
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $c->name }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-neutral">Tingkat {{ $c->grade_level ?? '-' }}</span>
                            </td>
                            <td>
                                <b style="color: var(--dark);">{{ $studentsCount }}</b> Siswa
                            </td>
                            <td>
                                <span style="font-size: 12px; color: var(--slate-600);">
                                    {{ $teachersList ?: 'Belum ada guru ditugaskan' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $c->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $c->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('admin.classes') }}" class="btn btn-secondary btn-sm">Kelola</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--slate-400); padding: 24px;">Belum ada kelas yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
