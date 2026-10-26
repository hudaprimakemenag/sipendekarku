<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">RELASI KELUARGA & WALI</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Wali Murid & Relasi Siswa</h1>
            <p style="color: var(--slate-500); font-size: 13px;">Kelola akun wali murid dan hubungkan dengan data ananda (mendukung relasi multi-anak).</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" wire:click="openCreate">
                + Tambah Wali Murid Baru
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
                        <th>Nama Wali Murid</th>
                        <th>Email Login</th>
                        <th>Anak yang Terhubung</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($parents as $p)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="user-avatar" style="background:#fef3c7; color:#b45309;">
                                        {{ strtoupper(substr($p->name, 0, 2)) }}
                                    </div>
                                    <strong style="color: var(--dark);">{{ $p->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span style="color: var(--slate-600); font-size: 13px;">{{ $p->email }}</span>
                            </td>
                            <td>
                                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                    @forelse ($p->children as $child)
                                        <span class="badge badge-neutral" style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 8px;">
                                            {{ $child->name }}
                                            <button type="button" style="border:none; background:transparent; cursor:pointer; color:var(--rose); font-size:12px;" wire:click="unlinkStudent({{ $p->id }}, {{ $child->id }})" title="Lepas Tautan">✕</button>
                                        </span>
                                    @empty
                                        <span style="color: var(--slate-400); font-size: 12px;">Belum ada siswa terhubung</span>
                                    @endforelse
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <button type="button" class="btn btn-sm btn-secondary" wire:click="openLink({{ $p->id }})">
                                    + Hubungkan Anak
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--slate-400); padding: 32px;">
                                Belum ada wali murid yang didaftarkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Parent Modal -->
    @if ($showCreateModal)
        <div class="modal-overlay">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">Tambah Wali Murid Baru</h3>
                    <button type="button" class="sidebar-close-btn" style="display:grid;" wire:click="$set('showCreateModal', false)">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Lengkap Wali Murid</label>
                        <input type="text" class="form-control" placeholder="Contoh: Bpk. Rahman" wire:model="name">
                        @error('name') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Alamat Email</label>
                        <input type="email" class="form-control" placeholder="wali@sekolah.id" wire:model="email">
                        @error('email') <span class="form-error">{{ $message }}</span> @enderror
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
                    <button type="button" class="btn btn-primary" wire:click="saveParent">Daftarkan Wali</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Link Student Modal -->
    @if ($showLinkModal)
        <div class="modal-overlay">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">Hubungkan Siswa ke Wali Murid</h3>
                    <button type="button" class="sidebar-close-btn" style="display:grid;" wire:click="$set('showLinkModal', false)">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih Siswa</label>
                        <select class="form-control" wire:model="selectedStudentId">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach ($students as $st)
                                <option value="{{ $st->id }}">{{ $st->name }} (NISN: {{ $st->student_number ?? '-' }})</option>
                            @endforeach
                        </select>
                        @error('selectedStudentId') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showLinkModal', false)">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="linkStudent">Hubungkan Siswa</button>
                </div>
            </div>
        </div>
    @endif
</div>
