<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">REKAPITULASI & EVALUASI</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Laporan Perkembangan Kelas</h1>
            <p style="color: var(--slate-500); font-size: 13px;">Rekap aktivitas, kepatuhan sholat 5 waktu, dan perolehan poin siswa di kelas yang diampu.</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            @if ($currentClass)
                <a href="{{ route('reports.export.csv', ['type' => 'class', 'class_id' => $currentClass->id, 'start' => $startDate, 'end' => $endDate]) }}" class="btn btn-secondary">
                    ⬇ Unduh Excel / CSV
                </a>
                <a href="{{ route('reports.printable', ['type' => 'class', 'class_id' => $currentClass->id, 'start' => $startDate, 'end' => $endDate]) }}" target="_blank" class="btn btn-primary">
                    🖨 Cetak / Simpan PDF
                </a>
            @endif
        </div>
    </div>

    <!-- Filter Section -->
    <div class="panel" style="padding: 16px 20px; margin-bottom: 20px;">
        <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 12px; font-weight: 700; color: var(--slate-700); display: block; margin-bottom: 4px;">Pilih Kelas</label>
                <select class="form-control" wire:model.live="selectedClassId">
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} (Tingkat {{ $c->grade_level }})</option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 160px;">
                <label style="font-size: 12px; font-weight: 700; color: var(--slate-700); display: block; margin-bottom: 4px;">Tanggal Mulai</label>
                <input type="date" class="form-control" wire:model.live="startDate">
            </div>

            <div style="flex: 1; min-width: 160px;">
                <label style="font-size: 12px; font-weight: 700; color: var(--slate-700); display: block; margin-bottom: 4px;">Tanggal Akhir</label>
                <input type="date" class="form-control" wire:model.live="endDate">
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">
                    Rekap Kelas {{ $currentClass?->name ?? '' }} ({{ count($reportData) }} Siswa)
                </h2>
                <small style="color: var(--slate-400);">Periode {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }} ({{ $daysCount }} Hari)</small>
            </div>
        </div>

        <div class="table-responsive" style="border: none;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Rank</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kepatuhan Shalat</th>
                        <th>Aktivitas Terisi</th>
                        <th>Poin Periode Ini</th>
                        <th style="text-align: right;">Akumulasi Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reportData as $idx => $row)
                        @php
                            $st = $row['student'];
                        @endphp
                        <tr>
                            <td>
                                @if ($idx === 0)
                                    <b class="medal gold" style="display:inline-grid;">1</b>
                                @elseif ($idx === 1)
                                    <b class="medal silver" style="display:inline-grid;">2</b>
                                @elseif ($idx === 2)
                                    <b class="medal bronze" style="display:inline-grid;">3</b>
                                @else
                                    <span style="color: var(--slate-500); font-weight: 600; font-size: 13px; margin-left: 6px;">#{{ $idx + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <strong style="color: var(--dark); font-size: 13px;">{{ $st->name }}</strong>
                            </td>
                            <td>
                                <span style="color: var(--slate-500); font-size: 12px;">{{ $st->student_number ?? '-' }}</span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="flex: 1; max-width: 100px; height: 8px; background: var(--slate-100); border-radius: 4px; overflow: hidden;">
                                        <div style="width: {{ $row['sholat_percent'] }}%; height: 100%; background: {{ $row['sholat_percent'] >= 80 ? 'var(--emerald)' : ($row['sholat_percent'] >= 50 ? 'var(--amber)' : 'var(--rose)') }}; border-radius: 4px;"></div>
                                    </div>
                                    <b style="font-size: 12px; color: var(--dark);">{{ $row['sholat_percent'] }}%</b>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $row['total_filled'] }} item</span>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--emerald-dark);">+{{ number_format($row['points_range'], 0, ',', '.') }}</span>
                            </td>
                            <td style="text-align: right;">
                                <strong style="font-size: 14px; color: var(--dark);">{{ number_format($row['total_points'], 0, ',', '.') }}</strong>
                                <small style="color: var(--slate-400);">pts</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--slate-400); padding: 32px;">
                                Tidak ada data siswa di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
