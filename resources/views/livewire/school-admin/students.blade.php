<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">PESERTA DIDIK & ROMBEL</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Data Siswa & Penempatan Kelas</h1>
            <p style="color: var(--slate-500); font-size: 13px;">Kelola akun siswa, nomor induk (NISN), dan penempatan ke kelas aktif per semester.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" wire:click="openCreate">
                + Tambah Siswa Baru
            </button>
        </div>
    </div>

    @if ($message)
        <div class="alert-box alert-success">
            ✓ {{ $message }}
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="panel" style="padding: 16px 20px; margin-bottom: 20px;">
        <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
            <div style="flex: 2; min-width: 240px;">
                <label style="font-size: 12px; font-weight: 700; color: var(--slate-700); display: block; margin-bottom: 4px;">Cari Siswa</label>
                <input type="text" class="form-control" placeholder="Cari nama, NISN, atau email..." wire:model.live.debounce.300ms="search">
            </div>

            <div style="flex: 1; min-width: 180px;">
                <label style="font-size: 12px; font-weight: 700; color: var(--slate-700); display: block; margin-bottom: 4px;">Filter Kelas</label>
                <select class="form-control" wire:model.live="filterClass">
                    <option value="all">Semua Kelas</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="table-responsive" style="border: none;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Email Login</th>
                        <th>Kelas Aktif</th>
                        <th>Total Poin</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $st)
                        @php
                            $currentClass = $st->currentClass($activeSemester);
                        @endphp
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="user-avatar">{{ strtoupper(substr($st->name, 0, 2)) }}</div>
                                    <strong style="color: var(--dark);">{{ $st->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span style="font-family: monospace; font-size: 12px; color: var(--slate-600);">
                                    {{ $st->student_number ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span style="color: var(--slate-500); font-size: 12px;">{{ $st->email }}</span>
                            </td>
                            <td>
                                @if ($currentClass)
                                    <span class="badge badge-success">{{ $currentClass->name }}</span>
                                @else
                                    <span class="badge badge-warning">Belum ada kelas</span>
                                @endif
                            </td>
                            <td>
                                <b style="color: var(--emerald-dark);">{{ number_format($st->totalVerifiedPoints(), 0, ',', '.') }}</b>
                                <small style="color: var(--slate-400);">pts</small>
                            </td>
                            <td style="text-align: right;">
                                <button type="button" class="btn btn-sm btn-secondary" wire:click="openAssign({{ $st->id }})">
                                    Pindah / Set Kelas
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--slate-400); padding: 32px;">
                                Tidak ada data siswa ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $students->links() }}
        </div>
    </div>

    <!-- Create Student Modal -->
    @if ($showCreateModal)
        <div class="modal-overlay">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">Tambah Siswa Baru</h3>
                    <button type="button" class="sidebar-close-btn" style="display:grid;" wire:click="$set('showCreateModal', false)">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Lengkap Siswa</label>
                        <input type="text" class="form-control" placeholder="Contoh: Naila Azzahra" wire:model="name">
                        @error('name') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>NISN / Nomor Induk</label>
                        <input type="text" class="form-control" placeholder="Contoh: 202605001" wire:model="studentNumber">
                        @error('studentNumber') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Alamat Email</label>
                        <input type="email" class="form-control" placeholder="siswa@sekolah.id" wire:model="email">
                        @error('email') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Penempatan Kelas Awal</label>
                        <select class="form-control" wire:model="initialClassId">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} (Tingkat {{ $c->grade_level }})</option>
                            @endforeach
                        </select>
                        @error('initialClassId') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Password Akun</label>
                        <input type="password" class="form-control" wire:model="password">
                        <p class="form-hint">Default: password.</p>
                        @error('password') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showCreateModal', false)">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="saveStudent">Daftarkan Siswa</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Assign Student Modal -->
    @if ($showAssignModal)
        <div class="modal-overlay">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">Penempatan Kelas Siswa</h3>
                    <button type="button" class="sidebar-close-btn" style="display:grid;" wire:click="$set('showAssignModal', false)">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih Kelas Tujuan</label>
                        <select class="form-control" wire:model="selectedClassId">
                            @foreach ($classes as $cl)
                                <option value="{{ $cl->id }}">{{ $cl->name }} (Tingkat {{ $cl->grade_level }})</option>
                            @endforeach
                        </select>
                        @error('selectedClassId') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Semester</label>
                        <select class="form-control" wire:model="selectedSemesterId">
                            @foreach ($semesters as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} (TA {{ $s->academicYear?->name }})</option>
                            @endforeach
                        </select>
                        @error('selectedSemesterId') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showAssignModal', false)">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="saveAssignment">Simpan Penempatan</button>
                </div>
            </div>
        </div>
    @endif
</div>
