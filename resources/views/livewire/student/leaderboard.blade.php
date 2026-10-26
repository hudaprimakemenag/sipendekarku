<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">LEADERBOARD & RIWAYAT POIN</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Ranking & Perolehan Poin</h1>
            <p style="color: var(--slate-500); font-size: 13px;">
                Kelas: <b>{{ $class?->name ?? '5A' }}</b> · Semester: <b>{{ $semester?->name ?? 'Ganjil' }}</b>
            </p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Left: Leaderboard -->
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">Klasemen Poin Kelas</h2>
                <span class="badge badge-success">{{ count($leaderboard) }} Siswa</span>
            </div>

            <div class="table-responsive" style="border: none;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Pos</th>
                            <th>Nama Siswa</th>
                            <th style="text-align: right;">Total Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leaderboard as $entry)
                            <tr style="{{ $entry['is_me'] ? 'background: var(--emerald-light); font-weight: 700;' : '' }}">
                                <td>
                                    @if ($entry['rank'] === 1)
                                        <b class="medal gold" style="display:inline-grid;">1</b>
                                    @elseif ($entry['rank'] === 2)
                                        <b class="medal silver" style="display:inline-grid;">2</b>
                                    @elseif ($entry['rank'] === 3)
                                        <b class="medal bronze" style="display:inline-grid;">3</b>
                                    @else
                                        <span style="color: var(--slate-500); font-size: 12px; margin-left: 6px;">#{{ $entry['rank'] }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div class="tiny-avatar {{ $entry['rank'] <= 3 ? 'violet' : '' }}">
                                            {{ strtoupper(substr($entry['student']->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <span>{{ $entry['student']->name }}</span>
                                            @if ($entry['is_me'])
                                                <small style="color: var(--emerald-dark); font-weight: 800; margin-left: 4px;">(Kamu)</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    <strong style="color: var(--dark); font-size: 14px;">
                                        {{ number_format($entry['total_points'], 0, ',', '.') }}
                                    </strong>
                                    <span style="font-size: 11px; color: var(--slate-400);">pts</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--slate-400); padding: 24px;">Belum ada siswa di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: My Point History -->
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">Riwayat Poin Pribadi</h2>
                <span class="badge badge-neutral">Total: {{ number_format(auth()->user()->totalVerifiedPoints(), 0, ',', '.') }} pts</span>
            </div>

            <div class="table-responsive" style="border: none;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal & Waktu</th>
                            <th>Kegiatan</th>
                            <th style="text-align: right;">Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($myPointLogs as $point)
                            <tr>
                                <td>
                                    <span style="font-weight: 600; color: var(--dark); font-size: 12px;">
                                        {{ $point->credited_at->translatedFormat('d M Y') }}
                                    </span>
                                    <small style="display: block; color: var(--slate-400); font-size: 11px;">
                                        {{ $point->credited_at->format('H:i') }} WIB
                                    </small>
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: var(--dark);">{{ $point->description ?? 'Kegiatan Terverifikasi' }}</span>
                                    @if ($point->verification?->teacher)
                                        <small style="display: block; color: var(--slate-500); font-size: 11px;">
                                            Verifikator: {{ $point->verification->teacher->name }}
                                        </small>
                                    @endif
                                </td>
                                <td style="text-align: right; color: var(--emerald-dark); font-weight: 800;">
                                    +{{ number_format($point->points, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--slate-400); padding: 24px;">Belum ada riwayat poin terverifikasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 12px;">
                {{ $myPointLogs->links('vendor.pagination.compact') }}
            </div>
        </div>
    </div>
</div>
