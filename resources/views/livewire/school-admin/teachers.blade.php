<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">PENGELOLAAN TENAGA PENDIDIK</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Data Guru & Penugasan Kelas</h1>
            <p style="color: var(--slate-500); font-size: 13px;">Kelola akun guru dan penugasan pengampu kelas per semester.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" wire:click="openCreate">
                + Tambah Guru Baru
            </button>
        </div>
    </div>

    @if ($message)
        <div class="alert-box alert-success">
            ✓ {{ $message }}
        </div>
    @endif

    <div class="panel">
        <div class="table-responsive" style="border: none;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Guru</th>
                        <th>Email Login</th>
                        <th>Kelas yang Diampu</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $t)
                        @php
                            $assignments = $t->teachingAssignments->whereNull('unassigned_at');
                        @endphp
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="user-avatar">{{ strtoupper(substr($t->name, 0, 2)) }}</div>
                                    <strong style="color: var(--dark);">{{ $t->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span style="color: var(--slate-600); font-size: 13px;">{{ $t->email }}</span>
                            </td>
                            <td>
                                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                    @forelse ($assignments as $a)
                                        <span class="badge badge-neutral" style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 8px;">
                                            {{ $a->schoolClass?->name }} ({{ $a->semester?->name }})
                                            <button type="button" style="border:none; background:transparent; cursor:pointer; color:var(--rose); font-size:12px;" wire:click="unassign({{ $a->id }})" title="Cabut Penugasan">✕</button>
                                        </span>
                                    @empty
                                        <span style="color: var(--slate-400); font-size: 12px;">Belum ada penugasan kelas</span>
                                    @endforelse
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <button type="button" class="btn btn-sm btn-secondary" wire:click="openAssign({{ $t->id }})">
                                    + Tugaskan ke Kelas
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--slate-400); padding: 32px;">
                                Belum ada guru yang didaftarkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Teacher Modal -->
    @if ($showCreateModal)
        <div class="modal-overlay">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">Tambah Guru Baru</h3>
                    <button type="button" class="sidebar-close-btn" style="display:grid;" wire:click="$set('showCreateModal', false)">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Lengkap Guru (beserta gelar)</label>
                        <input type="text" class="form-control" placeholder="Contoh: Ustadz Ahmad Fauzi, S.Pd.I" wire:model="name">
                        @error('name') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Alamat Email</label>
                        <input type="email" class="form-control" placeholder="guru@sekolah.id" wire:model="email">
                        @error('email') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Password Akun</label>
                        <input type="password" class="form-control" wire:model="password">
                        <p class="form-hint">Default: password (dapat diganti oleh guru setelah login).</p>
                        @error('password') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showCreateModal', false)">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="saveTeacher">Daftarkan Guru</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Assign Teacher Modal -->
    @if ($showAssignModal)
        <div class="modal-overlay">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">Penugasan Guru ke Kelas</h3>
                    <button type="button" class="sidebar-close-btn" style="display:grid;" wire:click="$set('showAssignModal', false)">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih Kelas</label>
                        <select class="form-control" wire:model="selectedClassId">
                            @foreach ($classes as $cl)
                                <option value="{{ $cl->id }}">{{ $cl->name }} (Tingkat {{ $cl->grade_level }})</option>
                            @endforeach
                        </select>
                        @error('selectedClassId') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Semester Penugasan</label>
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
                    <button type="button" class="btn btn-primary" wire:click="saveAssignment">Simpan Penugasan</button>
                </div>
            </div>
        </div>
    @endif
</div>
