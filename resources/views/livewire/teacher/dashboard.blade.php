<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">{{ now()->translatedFormat('l, d F Y') }} · Dashboard Guru</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Assalamu'alaikum, {{ $teacher->name }} 🌿</h1>
            <p style="color: var(--slate-500); font-size: 13px;">
                Pantau dan verifikasi mutaba'ah ibadah serta pembiasaan karakter siswa kelas ampu Anda.
            </p>
        </div>
        <div>
            <a href="{{ route('teacher.verification') }}" class="btn btn-primary">
                ✓ Buka Halaman Verifikasi
            </a>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <article class="metric points">
            <div class="metric-icon" style="background:#fffbeb; color:var(--amber-dark);">◷</div>
            <div>
                <span>Perlu Verifikasi Hari Ini</span>
                <strong style="color: var(--amber-dark);">{{ $pendingTodayCount }}</strong>
                <small>Kegiatan terisi belum diverifikasi</small>
            </div>
        </article>

        <article class="metric points">
            <div class="metric-icon" style="background:#fff1f2; color:var(--rose);">!</div>
            <div>
                <span>Pending Kemarin</span>
                <strong style="color: var(--rose);">{{ $pendingYesterdayCount }}</strong>
                <small>Kegiatan hari kemarin</small>
            </div>
        </article>

        <article class="metric progress">
            <div class="metric-icon">✓</div>
            <div>
                <span>Diverifikasi Pekan Ini</span>
                <strong>{{ $totalVerifiedWeek }}</strong>
                <small>Aktivitas disetujui</small>
            </div>
        </article>

        <article class="metric rank">
            <div class="metric-icon">👥</div>
            <div>
                <span>Total Siswa Ampu</span>
                <strong>{{ $totalStudents }}</strong>
                <small>Dari {{ $assignedClasses->count() }} kelas</small>
            </div>
        </article>
    </div>

    <!-- Assigned Classes Cards -->
    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Daftar Kelas yang Diampu</h2>
                <p style="font-size: 12px; color: var(--slate-500);">Tahun Ajaran {{ $school?->activeAcademicYear?->name ?? '2026/2027' }} · Semester {{ $semester?->name ?? 'Ganjil' }}</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
            @forelse ($assignedClasses as $assignment)
                @php
                    $schoolClass = $assignment->schoolClass;
                    $studentCount = $schoolClass?->classStudents?->whereNull('left_at')->count() ?? 0;
                @endphp
                <div style="border: 1px solid var(--line); border-radius: var(--radius-lg); padding: 20px; background: #ffffff; display: flex; flex-direction: column; justify-content: space-between; gap: 16px;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                            <span class="badge badge-success">Tingkat {{ $schoolClass?->grade_level ?? '5' }}</span>
                            <span style="font-size: 12px; color: var(--slate-500); font-weight: 600;">{{ $studentCount }} Siswa</span>
                        </div>
                        <h3 style="font-size: 18px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">
                            {{ $schoolClass?->name ?? 'Kelas' }}
                        </h3>
                        <p style="font-size: 12px; color: var(--slate-500);">SDIT Al-Hikmah</p>
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('teacher.verification', ['class_id' => $schoolClass?->id]) }}" class="btn btn-primary btn-sm" style="flex: 1;">
                            ✓ Verifikasi
                        </a>
                        <a href="{{ route('teacher.reports', ['class_id' => $schoolClass?->id]) }}" class="btn btn-secondary btn-sm" style="flex: 1;">
                            ▤ Laporan
                        </a>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; color: var(--slate-400); padding: 36px;">
                    Anda belum ditugaskan ke kelas manapun pada semester ini. Silakan hubungi Admin Sekolah.
                </div>
            @endforelse
        </div>
    </div>
</div>
