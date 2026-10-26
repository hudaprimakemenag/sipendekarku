<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">RIWAYAT MUTABA'AH</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Riwayat Kegiatan Harian</h1>
            <p style="color: var(--slate-500); font-size: 13px;">Catatan pelaksanaan ibadah, akhlak, dan sosial yang telah dilaporkan.</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <input type="month" class="form-control" style="width: auto;" wire:model.live="selectedMonth">
            <select class="form-control" style="width: auto;" wire:model.live="selectedGroup">
                <option value="all">Semua Kelompok</option>
                @foreach ($groups as $g)
                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <article class="metric points">
            <div class="metric-icon">✦</div>
            <div>
                <span>Poin Terverifikasi Bulan Ini</span>
                <strong>+{{ number_format($totalApprovedPoints, 0, ',', '.') }}</strong>
                <small>Bulan {{ \Carbon\Carbon::parse($selectedMonth.'-01')->translatedFormat('F Y') }}</small>
            </div>
        </article>
        <article class="metric progress">
            <div class="metric-icon" style="background:#ecfdf5;color:var(--emerald-dark);">✓</div>
            <div>
                <span>Hari Terisi</span>
                <strong>{{ $totalLoggedDays }} Hari</strong>
                <small>Dari total hari bulan berjalan</small>
            </div>
        </article>
    </div>

    <!-- Day by Day Cards -->
    <div style="display: flex; flex-direction: column; gap: 16px;">
        @forelse ($groupedLogs as $dateString => $dayLogs)
            @php
                $dateObj = \Carbon\Carbon::parse($dateString);
                $isToday = $dateObj->isToday();
            @endphp
            <div class="panel" style="margin-bottom: 0;">
                <div class="panel-header" style="border-bottom: 1px solid var(--line); padding-bottom: 12px; margin-bottom: 16px;">
                    <div>
                        <span class="eyebrow">{{ $dateObj->translatedFormat('l') }}</span>
                        <h2 style="font-size: 16px; font-weight: 800; color: var(--dark);">
                            {{ $dateObj->translatedFormat('d F Y') }}
                            @if ($isToday)
                                <span class="badge badge-success" style="margin-left: 8px;">Hari Ini</span>
                            @else
                                <span class="badge badge-neutral" style="margin-left: 8px;">Terkunci</span>
                            @endif
                        </h2>
                    </div>
                    <div>
                        <span class="badge badge-neutral">{{ $dayLogs->count() }} kegiatan tercatat</span>
                    </div>
                </div>

                <div class="table-responsive" style="border: none;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kegiatan</th>
                                <th>Kelompok</th>
                                <th>Laporan Siswa</th>
                                <th>Status Verifikasi</th>
                                <th>Catatan Guru</th>
                                <th style="text-align: right;">Poin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dayLogs as $log)
                                @php
                                    $item = $log->activityItem;
                                    $verification = $log->verification;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $item?->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-neutral">{{ $item?->activityGroup?->name }}</span>
                                    </td>
                                    <td>
                                        @if ($item?->input_type === 'checklist')
                                            @if ($log->value === '1' || $log->value === 'true')
                                                <span class="badge badge-success">✓ Terlaksana</span>
                                            @else
                                                <span class="badge badge-neutral">✕ Tidak Terlaksana</span>
                                            @endif
                                        @else
                                            <span style="font-weight: 600; color: var(--dark);">{{ $log->value ?: '-' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (! $verification || $verification->status === 'pending')
                                            <span class="badge badge-warning">◌ Menunggu Verifikasi</span>
                                        @elseif ($verification->status === 'approved')
                                            <span class="badge badge-success">✓ Disetujui</span>
                                        @else
                                            <span class="badge badge-danger">✕ Belum Disetujui</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($verification?->notes)
                                            <span style="font-size: 12px; color: var(--slate-600);">“{{ $verification->notes }}”</span>
                                            <small style="display:block; color:var(--slate-400);">oleh {{ $verification->teacher?->name ?? 'Guru' }}</small>
                                        @else
                                            <span style="color: var(--slate-400); font-size: 12px;">-</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right; font-weight: 700;">
                                        @if ($verification && $verification->status === 'approved')
                                            <span style="color: var(--emerald-dark);">+{{ $verification->credited_points }}</span>
                                        @else
                                            <span style="color: var(--slate-400);">0</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="panel" style="text-align: center; padding: 48px; color: var(--slate-400);">
                <span style="font-size: 32px; display: block; margin-bottom: 12px;">◴</span>
                <p>Tidak ada catatan mutaba'ah pada periode {{ \Carbon\Carbon::parse($selectedMonth.'-01')->translatedFormat('F Y') }}.</p>
            </div>
        @endforelse
    </div>
</div>
