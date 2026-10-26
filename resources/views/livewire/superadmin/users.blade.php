<div>
    {{-- Header --}}
    <div class="panel-header">
        <div>
            <p class="eyebrow">MANAJEMEN PENGGUNA SISTEM</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Kelola Semua Pengguna</h1>
            <p style="color: var(--slate-500); font-size: 13px;">Tambah, edit, dan kelola akun Admin Sekolah, Guru, Siswa, dan Wali dari semua tenant.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" wire:click="openCreate">
                + Tambah Pengguna
            </button>
        </div>
    </div>

    {{-- Alert --}}
    @if ($message)
        <div class="alert-box {{ $messageType === 'danger' ? 'alert-danger' : 'alert-success' }}" wire:key="msg-{{ now() }}">
            {{ $messageType === 'danger' ? '✕' : '✓' }} {{ $message }}
        </div>
    @endif

    {{-- Role Summary Cards --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
        @php
            $roleLabels = [
                'school_admin' => ['label' => 'Admin Sekolah', 'icon' => '🏫', 'color' => 'var(--violet-600, #7c3aed)'],
                'teacher'      => ['label' => 'Guru',         'icon' => '👨‍🏫', 'color' => 'var(--blue-600, #2563eb)'],
                'student'      => ['label' => 'Siswa',        'icon' => '🎒', 'color' => 'var(--emerald-dark, #059669)'],
                'parent'       => ['label' => 'Wali Murid',   'icon' => '👨‍👩‍👧', 'color' => 'var(--amber-600, #d97706)'],
            ];
        @endphp
        @foreach ($roleLabels as $key => $info)
            <div class="panel" style="padding: 16px; cursor: pointer; border: 2px solid {{ $filterRole === $key ? $info['color'] : 'transparent' }};"
                 wire:click="{{ $filterRole === $key ? '$set(\'filterRole\', \'\')' : '$set(\'filterRole\', \'' . $key . '\')' }}">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 24px;">{{ $info['icon'] }}</span>
                    <div>
                        <div style="font-size: 22px; font-weight: 800; color: {{ $info['color'] }};">{{ number_format($roleCounts[$key]) }}</div>
                        <div style="font-size: 12px; color: var(--slate-500);">{{ $info['label'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="panel" style="padding: 16px; margin-bottom: 20px;">
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" class="form-control" placeholder="🔍 Cari nama, email, NIS..." wire:model.live.debounce.300ms="search">
            </div>
            <div style="min-width: 160px;">
                <select class="form-control" wire:model.live="filterRole">
                    <option value="">Semua Role</option>
                    <option value="school_admin">Admin Sekolah</option>
                    <option value="teacher">Guru</option>
                    <option value="student">Siswa</option>
                    <option value="parent">Wali Murid</option>
                </select>
            </div>
            <div style="min-width: 180px;">
                <select class="form-control" wire:model.live="filterSchool">
                    <option value="">Semua Sekolah</option>
                    @foreach ($schools as $sch)
                        <option value="{{ $sch->id }}">{{ $sch->name }}</option>
                    @endforeach
                </select>
            </div>
            @if ($search || $filterRole || $filterSchool)
                <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('search', ''); $set('filterRole', ''); $set('filterSchool', '')">
                    ✕ Reset
                </button>
            @endif
        </div>
    </div>

    {{-- Table --}}
    <div class="panel">
        <div class="table-responsive" style="border: none;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Sekolah</th>
                        <th>NIS / No. HP</th>
                        <th>Terdaftar</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        @php
                            $userRole = $user->getRoleNames()->first();
                            $roleConfig = [
                                'school_admin' => ['label' => 'Admin Sekolah', 'class' => 'badge-primary'],
                                'teacher'      => ['label' => 'Guru',          'class' => 'badge-neutral'],
                                'student'      => ['label' => 'Siswa',         'class' => 'badge-success'],
                                'parent'       => ['label' => 'Wali Murid',    'class' => 'badge-warning'],
                                'superadmin'   => ['label' => 'Superadmin',    'class' => 'badge-danger'],
                            ];
                            $rc = $roleConfig[$userRole] ?? ['label' => $userRole ?? '-', 'class' => 'badge-neutral'];
                        @endphp
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="tiny-avatar {{ in_array($userRole, ['school_admin', 'superadmin']) ? 'violet' : '' }}">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <strong style="color: var(--dark); font-size: 13px;">{{ $user->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 12px; color: var(--slate-500);">{{ $user->email }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $rc['class'] }}">{{ $rc['label'] }}</span>
                            </td>
                            <td>
                                <span style="font-size: 12px; color: var(--slate-600);">{{ $user->school?->name ?? '—' }}</span>
                            </td>
                            <td>
                                @if ($user->student_number)
                                    <span style="font-size: 12px; font-family: monospace; color: var(--slate-500);">{{ $user->student_number }}</span>
                                @elseif ($user->phone)
                                    <span style="font-size: 12px; color: var(--slate-500);">{{ $user->phone }}</span>
                                @else
                                    <span style="color: var(--slate-300);">—</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size: 12px; color: var(--slate-400);">{{ $user->created_at->translatedFormat('d M Y') }}</span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 4px;">
                                    <button type="button" class="btn btn-sm btn-secondary" wire:click="openEdit({{ $user->id }})">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary" wire:click="resetPassword({{ $user->id }})"
                                            title="Reset password ke 'password'">
                                        🔑
                                    </button>
                                    @if ($userRole !== 'superadmin')
                                        <button type="button" class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $user->id }})">
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--slate-400); padding: 40px;">
                                Tidak ada pengguna yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div style="padding: 12px 16px; border-top: 1px solid var(--line);">
                {{ $users->links('vendor.pagination.compact') }}
            </div>
        @endif
    </div>

    {{-- Create / Edit Modal --}}
    @if ($showModal)
        <div class="modal-overlay">
            <div class="modal-dialog" style="max-width: 520px;">
                <div class="modal-header">
                    <h3 class="modal-title">{{ $editingUserId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h3>
                    <button type="button" class="sidebar-close-btn" style="display:grid;" wire:click="$set('showModal', false)">✕</button>
                </div>
                <div class="modal-body">
                    {{-- Role --}}
                    <div class="form-group">
                        <label>Role / Peran</label>
                        <select class="form-control" wire:model.live="role">
                            <option value="school_admin">Admin Sekolah</option>
                            <option value="teacher">Guru</option>
                            <option value="student">Siswa</option>
                            <option value="parent">Wali Murid</option>
                        </select>
                        @error('role') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- School --}}
                    <div class="form-group">
                        <label>Sekolah</label>
                        <select class="form-control" wire:model="schoolId">
                            <option value="">— Pilih Sekolah —</option>
                            @foreach ($schools as $sch)
                                <option value="{{ $sch->id }}">{{ $sch->name }}</option>
                            @endforeach
                        </select>
                        @error('schoolId') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                    <hr style="margin: 16px 0; border: none; border-top: 1px solid var(--line);">

                    {{-- Name --}}
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" class="form-control" placeholder="Nama lengkap pengguna" wire:model="name">
                        @error('name') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" placeholder="email@sekolah.id" wire:model="email">
                        @error('email') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Password --}}
                    <div class="form-group">
                        <label>Password {{ $editingUserId ? '(kosongkan jika tidak diubah)' : '' }}</label>
                        <input type="password" class="form-control"
                               placeholder="{{ $editingUserId ? 'Biarkan kosong untuk tidak mengubah' : 'Min. 6 karakter' }}"
                               wire:model="password">
                        @error('password') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- NIS (only for student) --}}
                    @if ($role === 'student')
                        <div class="form-group">
                            <label>NIS (Nomor Induk Siswa)</label>
                            <input type="text" class="form-control" placeholder="Contoh: 20240001" wire:model="studentNumber">
                            @error('studentNumber') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    {{-- Phone (for parent / teacher) --}}
                    @if (in_array($role, ['parent', 'teacher']))
                        <div class="form-group">
                            <label>No. HP / WhatsApp</label>
                            <input type="text" class="form-control" placeholder="Contoh: 08123456789" wire:model="phone">
                            @error('phone') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">Simpan</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirm Modal --}}
    @if ($showDeleteConfirm)
        <div class="modal-overlay">
            <div class="modal-dialog" style="max-width: 400px;">
                <div class="modal-header">
                    <h3 class="modal-title" style="color: var(--danger, #dc2626);">⚠ Hapus Pengguna</h3>
                    <button type="button" class="sidebar-close-btn" style="display:grid;" wire:click="$set('showDeleteConfirm', false)">✕</button>
                </div>
                <div class="modal-body">
                    <p style="color: var(--slate-600); font-size: 14px; margin: 0;">
                        Pengguna ini akan dihapus permanen beserta semua data terkait (log aktivitas, poin, dsb). Tindakan ini <strong>tidak dapat dibatalkan</strong>.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showDeleteConfirm', false)">Batal</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteUser" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deleteUser">Ya, Hapus Pengguna</span>
                        <span wire:loading wire:target="deleteUser">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
