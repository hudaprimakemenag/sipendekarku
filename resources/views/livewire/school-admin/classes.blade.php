<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">STRUKTUR KELAS & ROMBEL</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Daftar Kelas Sekolah</h1>
            <p style="color: var(--slate-500); font-size: 13px;">Kelola ruang kelas, tingkat pendidikan, dan pembagian rombel belajar siswa.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" wire:click="openCreate">
                + Tambah Kelas Baru
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
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Tahun Ajaran</th>
                        <th>Jumlah Siswa</th>
                        <th>Guru Pengampu</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classes as $c)
                        @php
                            $students = $c->classStudents->whereNull('left_at');
                            $teachers = $c->classTeachers->whereNull('unassigned_at');
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $c->name }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-neutral">Tingkat {{ $c->grade_level }}</span>
                            </td>
                            <td>
                                <span style="color: var(--slate-600); font-size: 12px;">{{ $c->academicYear?->name }}</span>
                            </td>
                            <td>
                                <b style="color: var(--dark);">{{ $students->count() }}</b> Siswa
                            </td>
                            <td>
                                <span style="font-size: 12px; color: var(--slate-600);">
                                    {{ $teachers->pluck('teacher.name')->join(', ') ?: 'Belum ada guru ditugaskan' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $c->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $c->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <button type="button" class="btn btn-sm btn-secondary" wire:click="openEdit({{ $c->id }})">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" wire:click="delete({{ $c->id }})" wire:confirm="Hapus kelas {{ $c->name }}?">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--slate-400); padding: 32px;">
                                Belum ada data kelas yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create/Edit Class Modal -->
    @if ($showModal)
        <div class="modal-overlay">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">{{ $editingClassId ? 'Edit Kelas' : 'Tambah Kelas Baru' }}</h3>
                    <button type="button" class="sidebar-close-btn" style="display:grid;" wire:click="$set('showModal', false)">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tahun Ajaran</label>
                        <select class="form-control" wire:model="academicYearId">
                            @foreach ($years as $y)
                                <option value="{{ $y->id }}">{{ $y->name }} {{ $y->is_active ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                        @error('academicYearId') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Nama Kelas</label>
                        <input type="text" class="form-control" placeholder="Contoh: Kelas 5A atau Kelas 1 Ali" wire:model="className">
                        @error('className') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Tingkat / Grade Level (1-12)</label>
                        <input type="number" min="1" max="12" class="form-control" wire:model="gradeLevel">
                        @error('gradeLevel') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="classIsActive" wire:model="isActive">
                        <label for="classIsActive" style="margin-bottom: 0; cursor: pointer;">Kelas Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="save">Simpan Kelas</button>
                </div>
            </div>
        </div>
    @endif
</div>
