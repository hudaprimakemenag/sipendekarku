<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">PORTAL WALI MURID</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Pemantauan Mutaba'ah Ananda</h1>
            <p style="color: var(--slate-500); font-size: 13px;">Lihat perkembangan ibadah, karakter akhlak, dan catatan dari guru pengampu.</p>
        </div>
        @if ($selectedChild)
            <div style="display: flex; gap: 12px; align-items: center;">
                <a href="{{ route('reports.export.csv', ['type' => 'student', 'student_id' => $selectedChild->id, 'timeframe' => $timeframe]) }}" class="btn btn-secondary">
                    ⬇ Unduh Rekap
                </a>
                <a href="{{ route('reports.printable', ['type' => 'student', 'student_id' => $selectedChild->id, 'timeframe' => $timeframe]) }}" target="_blank" class="btn btn-primary">
                    🖨 Cetak Raport
                </a>
            </div>
        @endif
    </div>

    <!-- Children Switcher -->
    @if ($children->count() > 1)
        <div style="display: flex; gap: 12px; margin-bottom: 24px; overflow-x: auto; padding-bottom: 4px;">
            @foreach ($children as $child)
                <button 
                    type="button" 
                    class="btn {{ $selectedChild?->id === $child->id ? 'btn-primary' : 'btn-secondary' }}"
                    wire:click="setChild({{ $child->id }})"
                    style="border-radius: var(--radius-full); padding: 8px 18px;"
                >
                    <span class="tiny-avatar" style="width: 22px; height: 22px; font-size: 10px;">
                        {{ strtoupper(substr($child->name, 0, 2)) }}
                    </span>
                    {{ $child->name }} ({{ $child->currentClass()?->name ?? 'Kelas' }})
                </button>
            @endforeach
        </div>
    @endif

    @if (! $selectedChild)
        <div class="panel" style="text-align: center; padding: 48px; color: var(--slate-400);">
            <p>Akun Anda belum terhubung dengan data siswa. Silakan hubungi pihak sekolah untuk menautkan akun ananda.</p>
        </div>
    @else
        <!-- Child Info Header Banner -->
        <div class="panel" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #ffffff; padding: 24px 28px; margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 56px; height: 56px; border-radius: 50%; background: #ffffff; color: var(--emerald-dark); display: grid; place-items: center; font-size: 20px; font-weight: 800;">
                        {{ strtoupper(substr($selectedChild->name, 0, 2)) }}
                    </div>
                    <div>
                        <span style="font-size: 12px; font-weight: 700; color: #a7f3d0; text-transform: uppercase; letter-spacing: 0.05em;">Ananda Siswa</span>
                        <h2 style="font-size: 22px; font-weight: 800; margin: 2px 0;">{{ $selectedChild->name }}</h2>
                        <p style="font-size: 13px; color: #ecfdf5;">
                            {{ $selectedChild->school?->name ?? 'SDIT Al-Hikmah' }} · {{ $childClass?->name ?? 'Kelas 5A' }} · NISN: {{ $selectedChild->student_number ?? '-' }}
                        </p>
                    </div>
                </div>

                <div style="display: flex; gap: 12px; background: rgba(255,255,255,0.15); padding: 6px; border-radius: var(--radius-md);">
                    <button type="button" class="btn btn-sm {{ $timeframe === 'daily' ? 'btn-primary' : 'btn-secondary' }}" wire:click="setTimeframe('daily')" style="{{ $timeframe === 'daily' ? 'background:#ffffff; color:var(--emerald-dark);' : 'background:transparent; color:#ffffff; border:none;' }}">
                        Harian
                    </button>
                    <button type="button" class="btn btn-sm {{ $timeframe === 'weekly' ? 'btn-primary' : 'btn-secondary' }}" wire:click="setTimeframe('weekly')" style="{{ $timeframe === 'weekly' ? 'background:#ffffff; color:var(--emerald-dark);' : 'background:transparent; color:#ffffff; border:none;' }}">
                        Mingguan
                    </button>
                    <button type="button" class="btn btn-sm {{ $timeframe === 'monthly' ? 'btn-primary' : 'btn-secondary' }}" wire:click="setTimeframe('monthly')" style="{{ $timeframe === 'monthly' ? 'background:#ffffff; color:var(--emerald-dark);' : 'background:transparent; color:#ffffff; border:none;' }}">
                        Bulanan
                    </button>
                </div>
            </div>
        </div>

        <!-- Metrics Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
            <article class="metric points">
                <div class="metric-icon">✦</div>
                <div>
                    <span>Total Poin Terverifikasi</span>
                    <strong>{{ number_format($verifiedPoints, 0, ',', '.') }}</strong>
                    <small>Semester {{ $childSemester?->name ?? 'Ganjil' }}</small>
                </div>
            </article>

            <article class="metric rank">
                <div class="metric-icon">♛</div>
                <div>
                    <span>Peringkat di Kelas</span>
                    <strong>#{{ $classRank }}</strong>
                    <small>Kelas {{ $childClass?->name ?? '5A' }}</small>
                </div>
            </article>

            <article class="metric progress">
                <div class="metric-icon" style="background:#ecfdf5;color:var(--emerald-dark);">🕌</div>
                <div>
                    <span>Kepatuhan Shalat 5 Waktu</span>
                    <strong>{{ $sholatRate }}%</strong>
                    <small>Pada periode terpilih</small>
                </div>
            </article>

            <article class="metric progress">
                <div class="metric-icon">✓</div>
                <div>
                    <span>Aktivitas Terlaksana</span>
                    <strong>{{ $totalActivities }}</strong>
                    <small>Tercatat & terlaporkan</small>
                </div>
            </article>
        </div>

        <!-- Content Grid: Activity logs & Teacher feedback -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
            <!-- Left: Activity Logs Table -->
            <div class="panel">
                <div class="panel-header">
                    <h3 class="panel-title">Detail Kegiatan yang Dilaporkan</h3>
                    <span class="badge badge-neutral">{{ $logs->count() }} Entri</span>
                </div>

                <div class="table-responsive" style="border: none;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kegiatan</th>
                                <th>Laporan</th>
                                <th>Verifikasi Guru</th>
                                <th style="text-align: right;">Poin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                @php
                                    $item = $log->activityItem;
                                    $v = $log->verification;
                                @endphp
                                <tr>
                                    <td>
                                        <span style="font-weight: 600; font-size: 12px; color: var(--dark);">
                                            {{ $log->logged_on->translatedFormat('d M Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $item?->name }}</strong>
                                        <small style="display: block; color: var(--slate-400);">{{ $item?->activityGroup?->name }}</small>
                                    </td>
                                    <td>
                                        @if ($item?->input_type === 'checklist')
                                            @if ($log->value === '1' || $log->value === 'true')
                                                <span class="badge badge-success">✓ Terlaksana</span>
                                            @else
                                                <span class="badge badge-neutral">✕ Belum</span>
                                            @endif
                                        @else
                                            <span style="font-weight: 600; color: var(--dark);">{{ $log->value ?: '-' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($v && $v->status === 'approved')
                                            <span class="badge badge-success">✓ Disetujui</span>
                                            @if ($v->notes)
                                                <small style="display: block; color: var(--slate-600); font-style: italic;">“{{ $v->notes }}”</small>
                                            @endif
                                        @elseif ($v && $v->status === 'rejected')
                                            <span class="badge badge-danger">✕ Ditolak</span>
                                        @else
                                            <span class="badge badge-warning">◌ Menunggu</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right; font-weight: 700;">
                                        @if ($v && $v->status === 'approved')
                                            <span style="color: var(--emerald-dark);">+{{ $v->credited_points }}</span>
                                        @else
                                            <span style="color: var(--slate-400);">0</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--slate-400); padding: 32px;">
                                        Belum ada aktivitas tercatat pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: 7-day chart & Teacher Messages -->
            <div>
                <!-- 7 Day Chart -->
                <article class="history-card" style="margin-bottom: 20px;">
                    <div class="section-head">
                        <div>
                            <p class="eyebrow">TREN 7 HARI</p>
                            <h2>Keaktifan Harian</h2>
                        </div>
                    </div>
                    <div class="chart">
                        <div class="bars">
                            @foreach ($weeklyBars as $bar)
                                <span style="height: {{ max(15, $bar['percent']) }}%" class="{{ $bar['is_today'] ? 'today-bar' : '' }}">
                                    <i>{{ $bar['percent'] }}%</i>
                                </span>
                            @endforeach
                        </div>
                        <div class="days">
                            @foreach ($weeklyBars as $bar)
                                <span>{{ $bar['day'] }}</span>
                            @endforeach
                        </div>
                    </div>
                </article>

                <!-- Teacher Notes List -->
                <div class="panel">
                    <div class="panel-header">
                        <h3 class="panel-title">Catatan Guru</h3>
                    </div>
                    @forelse ($recentNotes as $noteLog)
                        <div style="border-left: 3px solid var(--emerald); padding-left: 12px; margin-bottom: 16px;">
                            <p style="font-size: 13px; color: var(--dark); font-style: italic; margin-bottom: 4px;">
                                “{{ $noteLog->verification->notes }}”
                            </p>
                            <small style="color: var(--slate-500); font-size: 11px;">
                                Oleh {{ $noteLog->verification->teacher?->name ?? 'Guru' }} · {{ $noteLog->logged_on->translatedFormat('d M Y') }}
                            </small>
                        </div>
                    @empty
                        <p style="font-size: 12px; color: var(--slate-400); text-align: center; padding: 16px;">
                            Belum ada catatan khusus dari guru pada periode ini.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
