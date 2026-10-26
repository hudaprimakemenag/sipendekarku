<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">VERIFIKASI MUTABA'AH</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Verifikasi Kegiatan Siswa</h1>
            <p style="color: var(--slate-500); font-size: 13px;">Periksa laporan harian siswa, beri catatan evaluasi, dan setujui untuk menambahkan poin ke siswa.</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <button type="button" class="btn btn-primary" wire:click="approveAllFilled" wire:confirm="Yakin ingin menyetujui seluruh kegiatan yang telah diisi siswa pada tanggal ini?">
                ✓ Setujui Semua yang Terisi
            </button>
        </div>
    </div>

    @if ($feedbackMessage)
        <div class="alert-box alert-success" style="margin-bottom: 20px;">
            ✓ {{ $feedbackMessage }}
        </div>
    @endif

    <!-- Filter Bar -->
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

            <div style="flex: 1; min-width: 180px;">
                <label style="font-size: 12px; font-weight: 700; color: var(--slate-700); display: block; margin-bottom: 4px;">Tanggal Kegiatan</label>
                <input type="date" class="form-control" wire:model.live="selectedDate">
            </div>

            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 12px; font-weight: 700; color: var(--slate-700); display: block; margin-bottom: 4px;">Filter Siswa</label>
                <select class="form-control" wire:model.live="selectedStudentId">
                    <option value="all">Semua Siswa di Kelas</option>
                    @foreach ($students as $st)
                        <option value="{{ $st->id }}">{{ $st->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Student Cards with Logs -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        @forelse ($students as $student)
            @if ($selectedStudentId === 'all' || (int)$selectedStudentId === $student->id)
                @php
                    $studentLogs = $groupedLogs->get($student->id) ?? collect();
                    $filledCount = $studentLogs->filter(fn($l) => $l->value === '1' || ($l->value && $l->value !== '0'))->count();
                    $approvedCount = $studentLogs->filter(fn($l) => $l->verification?->status === 'approved')->count();
                @endphp
                <div class="panel" style="margin-bottom: 0;">
                    <div class="panel-header" style="border-bottom: 1px solid var(--line); padding-bottom: 14px; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="user-avatar">{{ strtoupper(substr($student->name, 0, 2)) }}</div>
                            <div>
                                <h2 style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 2px;">
                                    {{ $student->name }}
                                </h2>
                                <small style="color: var(--slate-400);">NISN: {{ $student->student_number ?? '-' }} · Poin Semester: {{ $student->totalVerifiedPoints() }} pts</small>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <span class="badge badge-neutral">Terisi: {{ $filledCount }}</span>
                            <span class="badge {{ $approvedCount > 0 ? 'badge-success' : 'badge-warning' }}">Disetujui: {{ $approvedCount }}</span>
                        </div>
                    </div>

                    @if ($studentLogs->isEmpty())
                        <div style="padding: 24px; text-align: center; color: var(--slate-400); font-size: 13px;">
                            Siswa belum mengisi mutaba'ah pada tanggal {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}.
                        </div>
                    @else
                        <div class="table-responsive" style="border: none;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="width: 25%;">Kegiatan</th>
                                        <th style="width: 25%;">Laporan Siswa</th>
                                        <th style="width: 25%;">Catatan Guru</th>
                                        <th style="width: 25%; text-align: right;">Aksi Verifikasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($studentLogs as $log)
                                        @php
                                            $item = $log->activityItem;
                                            $verification = $log->verification;
                                            $isApproved = $verification?->status === 'approved';
                                            $isRejected = $verification?->status === 'rejected';
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $item?->name }}</strong>
                                                <small style="display: block; color: var(--slate-400);">
                                                    {{ $item?->activityGroup?->name }} · +{{ $item?->point_weight }} Poin
                                                </small>
                                            </td>
                                            <td>
                                                @if ($item?->input_type === 'checklist')
                                                    @if ($log->value === '1' || $log->value === 'true')
                                                        <span class="badge badge-success">✓ Terlaksana</span>
                                                    @else
                                                        <span class="badge badge-neutral">✕ Tidak Terlaksana</span>
                                                    @endif
                                                @else
                                                    <span style="font-weight: 700; color: var(--dark);">{{ $log->value ?: '-' }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    style="padding: 6px 10px; font-size: 12px;"
                                                    placeholder="Tulis catatan (opsional)..."
                                                    wire:model.defer="notes.{{ $log->id }}"
                                                >
                                            </td>
                                            <td style="text-align: right;">
                                                <div style="display: inline-flex; gap: 6px;">
                                                    <button 
                                                        type="button" 
                                                        class="btn btn-sm {{ $isApproved ? 'btn-primary' : 'btn-secondary' }}"
                                                        wire:click="approve({{ $log->id }})"
                                                        title="Setujui dan beri poin"
                                                    >
                                                        ✓ Setujui
                                                    </button>
                                                    <button 
                                                        type="button" 
                                                        class="btn btn-sm {{ $isRejected ? 'btn-danger' : 'btn-secondary' }}"
                                                        wire:click="reject({{ $log->id }})"
                                                        title="Tolak laporan ini"
                                                    >
                                                        ✕ Tolak
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif
        @empty
            <div class="panel" style="text-align: center; padding: 48px; color: var(--slate-400);">
                Tidak ada data siswa ditemukan di kelas ini.
            </div>
        @endforelse
    </div>
</div>
