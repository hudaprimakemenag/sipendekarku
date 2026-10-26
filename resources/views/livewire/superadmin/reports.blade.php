<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">REKAPITULASI PUSAT</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Laporan Global Lintas Sekolah</h1>
            <p style="color: var(--slate-500); font-size: 13px;">Komparasi tingkat keaktifan mutaba'ah dan akumulasi pembiasaan karakter antar sekolah.</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('reports.export.csv', ['type' => 'global']) }}" class="btn btn-secondary">
                ⬇ Unduh Excel / CSV
            </a>
            <a href="{{ route('reports.printable', ['type' => 'global']) }}" target="_blank" class="btn btn-primary">
                🖨 Cetak Laporan Global
            </a>
        </div>
    </div>

    <div class="panel">
        <div class="table-responsive" style="border: none;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Pos</th>
                        <th>Sekolah</th>
                        <th>Kode</th>
                        <th>Siswa</th>
                        <th>Guru</th>
                        <th>Aktivitas Terisi</th>
                        <th>Rata-rata Poin/Siswa</th>
                        <th style="text-align: right;">Total Poin Terverifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schoolSummaries as $idx => $row)
                        @php
                            $sch = $row['school'];
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
                                <strong style="color: var(--dark); font-size: 14px;">{{ $sch->name }}</strong>
                                <small style="display: block; color: var(--slate-400);">{{ $sch->address ?: 'Alamat belum diatur' }}</small>
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $sch->code }}</span>
                            </td>
                            <td>
                                <b>{{ $row['student_count'] }}</b> Siswa
                            </td>
                            <td>
                                <b>{{ $row['teacher_count'] }}</b> Guru
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ number_format($row['total_logs'], 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <b style="color: var(--dark);">{{ number_format($row['avg_points'], 0, ',', '.') }}</b> pts
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
                            <td colspan="8" style="text-align: center; color: var(--slate-400); padding: 32px;">
                                Belum ada data sekolah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
