<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">PORTAL SUPERADMIN GLOBAL</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Dashboard Pusat AMALIN</h1>
            <p style="color: var(--slate-500); font-size: 13px;">Kelola multi-sekolah (multi-tenant), master katalog kegiatan global, dan pantau statistik lintas institusi.</p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('superadmin.schools') }}" class="btn btn-secondary">
                + Tambah Sekolah
            </a>
            <a href="{{ route('superadmin.catalog') }}" class="btn btn-primary">
                📋 Kelola Katalog Kegiatan
            </a>
        </div>
    </div>

    <!-- Global Metrics Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <article class="metric points">
            <div class="metric-icon" style="background:#d1fae5; color:var(--emerald-dark);">🏢</div>
            <div>
                <span>Total Sekolah</span>
                <strong>{{ $totalSchools }}</strong>
                <small>{{ $activeSchools }} sekolah aktif</small>
            </div>
        </article>

        <article class="metric points">
            <div class="metric-icon" style="background:#dbeafe; color:#1d4ed8;">🤼</div>
            <div>
                <span>Total Siswa</span>
                <strong>{{ $totalStudents }}</strong>
                <small>Lintas seluruh sekolah</small>
            </div>
        </article>

        <article class="metric points">
            <div class="metric-icon" style="background:#e0e7ff; color:#4338ca;">♟</div>
            <div>
                <span>Guru & Admin</span>
                <strong>{{ $totalTeachers + $totalSchoolAdmins }}</strong>
                <small>{{ $totalTeachers }} guru · {{ $totalSchoolAdmins }} admin</small>
            </div>
        </article>

        <article class="metric progress">
            <div class="metric-icon">✓</div>
            <div>
                <span>Aktivitas Hari Ini</span>
                <strong>{{ number_format($todayLogs, 0, ',', '.') }}</strong>
                <small>Laporan terisi hari ini</small>
            </div>
        </article>
    </div>

    <!-- Quick Navigation Panels -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <a href="{{ route('superadmin.schools') }}" style="text-decoration: none; color: inherit;">
            <div class="panel" style="padding: 20px; border-left: 4px solid var(--emerald); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 20px;">🏢</span>
                    <span class="badge badge-success">{{ $totalSchools }} Sekolah</span>
                </div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">Kelola Multi-Sekolah</h3>
                <p style="font-size: 12px; color: var(--slate-500);">Tambah data sekolah baru, atur timezone, buat akun admin sekolah, dan pantau status tenant.</p>
            </div>
        </a>

        <a href="{{ route('superadmin.catalog') }}" style="text-decoration: none; color: inherit;">
            <div class="panel" style="padding: 20px; border-left: 4px solid var(--amber); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 20px;">📋</span>
                    <span class="badge badge-warning">{{ $totalItems }} Kegiatan</span>
                </div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">Katalog Kegiatan Global</h3>
                <p style="font-size: 12px; color: var(--slate-500);">Kelola kelompok kegiatan (Ibadah, Akhlak, Sosial) dan master jenis kegiatan serta bobot poin.</p>
            </div>
        </a>

        <a href="{{ route('superadmin.reports') }}" style="text-decoration: none; color: inherit;">
            <div class="panel" style="padding: 20px; border-left: 4px solid #6366f1; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 20px;">▤</span>
                    <span class="badge badge-neutral">Laporan</span>
                </div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">Laporan Lintas Sekolah</h3>
                <p style="font-size: 12px; color: var(--slate-500);">Komparasi keaktifan antar sekolah dan ekspor laporan rekapitulasi agregat.</p>
            </div>
        </a>
    </div>

    <!-- Registered Schools List -->
    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Daftar Sekolah Terdaftar</h2>
                <small style="color: var(--slate-400);">Menampilkan seluruh entitas sekolah di sistem</small>
            </div>
            <a href="{{ route('superadmin.schools') }}" class="btn btn-secondary btn-sm">Kelola Sekolah →</a>
        </div>

        <div class="table-responsive" style="border: none;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Sekolah</th>
                        <th>Alamat</th>
                        <th>Zona Waktu</th>
                        <th>Jumlah Pengguna</th>
                        <th>Jumlah Kelas</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schools as $sch)
                        <tr>
                            <td>
                                <span class="badge badge-neutral">{{ $sch->code }}</span>
                            </td>
                            <td>
                                <strong style="color: var(--dark);">{{ $sch->name }}</strong>
                            </td>
                            <td>
                                <span style="color: var(--slate-500); font-size: 12px;">{{ $sch->address ?? '-' }}</span>
                            </td>
                            <td>
                                <span style="color: var(--slate-600); font-size: 12px;">{{ $sch->timezone }}</span>
                            </td>
                            <td>
                                <b style="color: var(--dark);">{{ $sch->users_count }}</b> Pengguna
                            </td>
                            <td>
                                <b style="color: var(--dark);">{{ $sch->classes_count }}</b> Kelas
                            </td>
                            <td>
                                <span class="badge {{ $sch->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $sch->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--slate-400); padding: 32px;">Belum ada sekolah terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
