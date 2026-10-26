<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">PENGELOLAAN MULTI-SEKOLAH</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Data Institusi Sekolah (Tenants)</h1>
            <p style="color: var(--slate-500); font-size: 13px;">Kelola pendaftaran sekolah, zonasi waktu, dan status aktivasi tenant di seluruh Indonesia.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" wire:click="openCreate">
                + Daftarkan Sekolah Baru
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
                        <th>Kode</th>
                        <th>Nama Sekolah</th>
                        <th>Alamat</th>
                        <th>Zona Waktu</th>
                        <th>Total Pengguna</th>
                        <th>Total Kelas</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schools as $sch)
                        <tr>
                            <td>
                                <span class="badge badge-neutral" style="font-family: monospace;">{{ $sch->code }}</span>
                            </td>
                            <td>
                                <strong style="color: var(--dark); font-size: 14px;">{{ $sch->name }}</strong>
                            </td>
                            <td>
                                <span style="color: var(--slate-500); font-size: 12px;">{{ $sch->address ?: '-' }}</span>
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $sch->timezone }}</span>
                            </td>
                            <td>
                                <b style="color: var(--dark);">{{ $sch->users_count }}</b> Pengguna
                            </td>
                            <td>
                                <b style="color: var(--dark);">{{ $sch->classes_count }}</b> Kelas
                            </td>
                            <td>
                                <span class="badge {{ $sch->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $sch->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <button type="button" class="btn btn-sm btn-secondary" wire:click="openEdit({{ $sch->id }})">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-sm {{ $sch->is_active ? 'btn-danger' : 'btn-primary' }}" wire:click="toggleActive({{ $sch->id }})">
                                        {{ $sch->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--slate-400); padding: 32px;">
                                Belum ada sekolah terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if ($showModal)
        <div class="modal-overlay">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">{{ $editingSchoolId ? 'Edit Data Sekolah' : 'Daftarkan Sekolah Baru' }}</h3>
                    <button type="button" class="sidebar-close-btn" style="display:grid;" wire:click="$set('showModal', false)">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Unik Sekolah</label>
                        <input type="text" class="form-control" placeholder="Contoh: SDIT-001" wire:model="code">
                        @error('code') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Nama Sekolah</label>
                        <input type="text" class="form-control" placeholder="Contoh: SDIT Al-Hikmah" wire:model="name">
                        @error('name') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Alamat Lengkap</label>
                        <textarea class="form-control" rows="2" placeholder="Alamat sekolah..." wire:model="address"></textarea>
                        @error('address') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Zona Waktu (Timezone)</label>
                        <select class="form-control" wire:model="timezone">
                            <option value="Asia/Jakarta">WIB (Asia/Jakarta)</option>
                            <option value="Asia/Makassar">WITA (Asia/Makassar)</option>
                            <option value="Asia/Jayapura">WIT (Asia/Jayapura)</option>
                        </select>
                        @error('timezone') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="schoolIsActive" wire:model="isActive">
                        <label for="schoolIsActive" style="margin-bottom: 0; cursor: pointer;">Status Sekolah Aktif</label>
                    </div>

                    @if (! $editingSchoolId)
                        <hr style="margin: 20px 0; border: none; border-top: 1px solid var(--line);">
                        <h4 style="font-size: 13px; font-weight: 800; color: var(--dark); margin-bottom: 12px;">Akun Admin Sekolah Awal (Opsional)</h4>
                        <div class="form-group">
                            <label>Nama Admin Sekolah</label>
                            <input type="text" class="form-control" placeholder="Contoh: Admin SDIT Al-Hikmah" wire:model="adminName">
                        </div>
                        <div class="form-group">
                            <label>Email Admin Sekolah</label>
                            <input type="email" class="form-control" placeholder="admin@sekolah.id" wire:model="adminEmail">
                            @error('adminEmail') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Password Awal</label>
                            <input type="password" class="form-control" wire:model="adminPassword">
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="save">Simpan Sekolah</button>
                </div>
            </div>
        </div>
    @endif
</div>
