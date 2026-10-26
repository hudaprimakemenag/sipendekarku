<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">REKAPITULASI SEKOLAH</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Laporan Sekolah & Leaderboard</h1>
            <p style="color: var(--slate-500); font-size: 13px;">
                Tahun Ajaran {{ $academicYear?->name ?? '2026/2027' }} · Semester {{ $semester?->name ?? 'Ganjil' }}
            </p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('reports.export.csv', ['type' => 'school', 'class_id' => $selectedClassId]) }}" class="btn btn-secondary">
                ⬇ Unduh Excel / CSV
            </a>
            <a href="{{ route('reports.printable', ['type' => 'school', 'class_id' => $selectedClassId]) }}" target="_blank" class="btn btn-primary">
                🖨 Cetak Raport Sekolah
            </a>
        </div>
    </div>

    <!-- Class Comparison Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px;">
        @foreach ($classSummaries as $cs)
            <div class="panel" style="margin-bottom: 0; padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <span class="badge badge-neutral">Tingkat {{ $cs['class']->grade_level }}</span>
                    <strong style="font-size: 14px; color: var(--emerald-dark);">{{ number_format($cs['total_points'], 0, ',', '.') }} pts</strong>
                </div>
                <h3 style="font-size: 18px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">
                    {{ $cs['class']->name }}
                </h3>
                <p style="font-size: 12px; color: var(--slate-500); margin-bottom: 12px;">
                    {{ $cs['student_count'] }} Siswa · Rata-rata: <b>{{ number_format($cs['avg_points'], 0, ',', '.') }} pts/siswa</b>
                </p>
            </div>
        @endforeach
    </div>

    <!-- Student Leaderboard Table -->
    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Peringkat Akumulasi Poin Siswa</h2>
                <small style="color: var(--slate-400);">Menampilkan seluruh siswa sekolah berdasarkan poin terverifikasi</small>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <label style="font-size: 12px; font-weight: 700; color: var(--slate-700);">Filter Kelas:</label>
                <select class="form-control" style="width: auto;" wire:model.live="selectedClassId">
                    <option value="all">Semua Kelas</option>
                    @foreach ($classes as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="table-responsive" style="border: none;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Rank</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th style="text-align: right;">Total Poin Terverifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rankedStudents as $idx => $row)
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
                                    <span style="color: var(--slate-500); font-weight: 700; font-size: 13px; margin-left: 6px;">#{{ $idx + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="user-avatar {{ $idx < 3 ? 'violet' : '' }}">{{ strtoupper(substr($st->name, 0, 2)) }}</div>
                                    <strong style="color: var(--dark);">{{ $st->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span style="font-family: monospace; font-size: 12px; color: var(--slate-500);">
                                    {{ $st->student_number ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $row['class']?->name ?? 'Tanpa Kelas' }}</span>
                            </td>
                            <td style="text-align: right;">
                                <strong style="font-size: 15px; color: var(--emerald-dark);">
                                    {{ number_format($row['total_points'], 0, ',', '.') }}
                                </strong>
                                <small style="color: var(--slate-400);">pts</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--slate-400); padding: 32px;">
                                Tidak ada data siswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
