<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — SIPENDEKARKU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #0f172a;
            padding: 24px;
            font-size: 12px;
            line-height: 1.5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            padding: 36px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }
        .header h2 {
            font-size: 15px;
            font-weight: 700;
            color: #059669;
            margin-bottom: 4px;
        }
        .header p {
            font-size: 11px;
            color: #64748b;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
            background: #f8fafc;
            padding: 12px 16px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .meta-item {
            font-size: 12px;
        }
        .meta-item span {
            color: #64748b;
            display: inline-block;
            width: 110px;
        }
        .meta-item strong {
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background: #f1f5f9;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            font-size: 10px;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-top: 40px;
            padding-top: 20px;
            text-align: center;
        }
        .sig-box {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .sig-space {
            height: 60px;
        }
        .print-btn-bar {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 99;
        }
        .btn-print {
            background: #059669;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(5,150,105,0.4);
        }
        @media print {
            body { background: #ffffff; padding: 0; }
            .container { box-shadow: none; padding: 0; max-width: 100%; }
            .print-btn-bar { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="print-btn-bar">
        <button class="btn-print" onclick="window.print()">🖨 Cetak Dokumen</button>
    </div>

    <div class="container">
        <!-- Official Header -->
        <div class="header">
            <h1>{{ $school?->name ?? 'SIPENDEKARKU PUSAT' }}</h1>
            <h2>{{ $title }}</h2>
            <p>{{ $school?->address ?? 'Sistem Informasi Pendidikan Karakter Anak Kulon Progo' }}</p>
        </div>

        @if ($type === 'student')
            <div class="meta-grid">
                <div class="meta-item"><span>Nama Siswa:</span> <strong>{{ $student->name }}</strong></div>
                <div class="meta-item"><span>NISN:</span> <strong>{{ $student->student_number ?? '-' }}</strong></div>
                <div class="meta-item"><span>Kelas:</span> <strong>{{ $student->currentClass()?->name ?? '5A' }}</strong></div>
                <div class="meta-item"><span>Total Poin:</span> <strong>{{ number_format($student->totalVerifiedPoints(), 0, ',', '.') }} pts</strong></div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 20%;">Tanggal</th>
                        <th>Kegiatan</th>
                        <th>Kelompok</th>
                        <th>Laporan Siswa</th>
                        <th>Status</th>
                        <th>Catatan Guru</th>
                        <th style="text-align: right;">Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ $log->logged_on->translatedFormat('d M Y') }}</td>
                            <td><strong>{{ $log->activityItem?->name }}</strong></td>
                            <td>{{ $log->activityItem?->activityGroup?->name }}</td>
                            <td>
                                @if ($log->activityItem?->input_type === 'checklist')
                                    {{ $log->value === '1' ? 'Terlaksana' : 'Tidak' }}
                                @else
                                    {{ $log->value ?: '-' }}
                                @endif
                            </td>
                            <td>
                                {{ $log->verification?->status === 'approved' ? 'Disetujui' : ($log->verification?->status === 'rejected' ? 'Ditolak' : 'Menunggu') }}
                            </td>
                            <td>{{ $log->verification?->notes ?: '-' }}</td>
                            <td style="text-align: right; font-weight: 700;">
                                +{{ $log->verification?->status === 'approved' ? $log->verification->credited_points : 0 }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        @elseif ($type === 'class' || $type === 'school')
            <div class="meta-grid">
                <div class="meta-item"><span>Institusi:</span> <strong>{{ $school?->name ?? 'SDIT Al-Hikmah' }}</strong></div>
                <div class="meta-item"><span>Tanggal Cetak:</span> <strong>{{ now()->translatedFormat('d F Y') }}</strong></div>
                @if ($type === 'class')
                    <div class="meta-item"><span>Kelas:</span> <strong>{{ $class->name }}</strong></div>
                @endif
                <div class="meta-item"><span>Jumlah Siswa:</span> <strong>{{ count($ranked) }} Siswa</strong></div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">Rank</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        @if ($type === 'school')
                            <th>Kelas</th>
                        @endif
                        <th style="text-align: right;">Total Poin Terverifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ranked as $idx => $row)
                        <tr>
                            <td style="text-align: center; font-weight: 700;">{{ $idx + 1 }}</td>
                            <td><strong>{{ $row['student']->name }}</strong></td>
                            <td>{{ $row['student']->student_number ?? '-' }}</td>
                            @if ($type === 'school')
                                <td>{{ $row['class'] }}</td>
                            @endif
                            <td style="text-align: right; font-weight: 700;">
                                {{ number_format($row['points'], 0, ',', '.') }} pts
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        @elseif ($type === 'global')
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">Pos</th>
                        <th>Nama Sekolah</th>
                        <th>Kode</th>
                        <th>Jumlah Siswa</th>
                        <th>Aktivitas Terisi</th>
                        <th style="text-align: right;">Total Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($summaries as $idx => $sch)
                        <tr>
                            <td style="text-align: center; font-weight: 700;">{{ $idx + 1 }}</td>
                            <td><strong>{{ $sch['school']->name }}</strong></td>
                            <td>{{ $sch['school']->code }}</td>
                            <td>{{ $sch['students'] }} Siswa</td>
                            <td>{{ number_format($sch['logs'], 0, ',', '.') }}</td>
                            <td style="text-align: right; font-weight: 700;">
                                {{ number_format($sch['points'], 0, ',', '.') }} pts
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Signatures Section -->
        <div class="signatures">
            <div class="sig-box">
                <p>Mengetahui,</p>
                <p><strong>Wali Kelas / Guru Pengampu</strong></p>
                <div class="sig-space"></div>
                <p>( ..................................................... )</p>
            </div>
            <div class="sig-box">
                <p>{{ $school?->name ? 'Jakarta' : 'Pusat' }}, {{ now()->translatedFormat('d F Y') }}</p>
                <p><strong>Kepala Sekolah / Administrator</strong></p>
                <div class="sig-space"></div>
                <p>( ..................................................... )</p>
            </div>
        </div>
    </div>
</body>
</html>
