<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">PENGATURAN KALENDER AKADEMIK</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Tahun Ajaran & Semester</h1>
            <p style="color: var(--slate-500); font-size: 13px;">Kelola periode tahun ajaran aktif dan semester berjalan di sekolah Anda.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" wire:click="openCreateYear">
                + Tambah Tahun Ajaran
            </button>
        </div>
    </div>

    @if ($message)
        <div class="alert-box alert-success">
            ✓ {{ $message }}
        </div>
    @endif

    <div style="display: flex; flex-direction: column; gap: 20px;">
        @forelse ($years as $yr)
            <div class="panel" style="margin-bottom: 0;">
                <div class="panel-header" style="border-bottom: 1px solid var(--line); padding-bottom: 14px; margin-bottom: 16px;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <h2 style="font-size: 18px; font-weight: 800; color: var(--dark);">
                                Tahun Ajaran {{ $yr->name }}
                            </h2>
                            @if ($yr->is_active)
                                <span class="badge badge-success">✓ Aktif Sekarang</span>
                            @else
                                <button type="button" class="btn btn-sm btn-secondary" wire:click="toggleYearActive({{ $yr->id }})">
                                    Set Sebagai Aktif
                                </button>
                            @endif
                        </div>
                        <small style="color: var(--slate-400);">
                            Periode: {{ $yr->starts_on->translatedFormat('d M Y') }} s/d {{ $yr->ends_on->translatedFormat('d M Y') }}
                        </small>
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="openEditYear({{ $yr->id }})">
                            Edit TA
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" wire:click="openCreateSemester({{ $yr->id }})">
                            + Tambah Semester
                        </button>
                    </div>
                </div>

                <!-- Semesters in this Year -->
                <div class="table-responsive" style="border: none;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama Semester</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($yr->semesters as $sem)
                                <tr>
                                    <td>
                                        <strong>Semester {{ $sem->name }}</strong>
                                    </td>
                                    <td>{{ $sem->starts_on->translatedFormat('d M Y') }}</td>
                                    <td>{{ $sem->ends_on->translatedFormat('d M Y') }}</td>
                                    <td>
                                        @if ($sem->is_active)
                                            <span class="badge badge-success">✓ Semester Aktif</span>
                                        @else
                                            <span class="badge badge-neutral">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        @if (! $sem->is_active)
                                            <button type="button" class="btn btn-sm btn-secondary" wire:click="toggleSemesterActive({{ $sem->id }})">
                                                Aktifkan
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--slate-400); padding: 16px;">
                                        Belum ada semester yang ditambahkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="panel" style="text-align: center; padding: 48px; color: var(--slate-400);">
                Belum ada data Tahun Ajaran. Klik tombol "+ Tambah Tahun Ajaran" untuk memulai.
            </div>
        @endforelse
    </div>

    <!-- Year Modal -->
    @if ($showYearModal)
        <div class="modal-overlay">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">{{ $editingYearId ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran Baru' }}</h3>
                    <button type="button" class="sidebar-close-btn" style="display:grid;" wire:click="$set('showYearModal', false)">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Tahun Ajaran</label>
                        <input type="text" class="form-control" placeholder="Contoh: 2026/2027" wire:model="yearName">
                        @error('yearName') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" class="form-control" wire:model="yearStartsOn">
                        @error('yearStartsOn') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" class="form-control" wire:model="yearEndsOn">
                        @error('yearEndsOn') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="yearIsActive" wire:model="yearIsActive">
                        <label for="yearIsActive" style="margin-bottom: 0; cursor: pointer;">Jadikan sebagai Tahun Ajaran Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showYearModal', false)">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="saveYear">Simpan Tahun Ajaran</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Semester Modal -->
    @if ($showSemesterModal)
        <div class="modal-overlay">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">Tambah Semester</h3>
                    <button type="button" class="sidebar-close-btn" style="display:grid;" wire:click="$set('showSemesterModal', false)">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Semester</label>
                        <select class="form-control" wire:model="semesterName">
                            <option value="Ganjil">Semester Ganjil</option>
                            <option value="Genap">Semester Genap</option>
                        </select>
                        @error('semesterName') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Tanggal Mulai Semester</label>
                        <input type="date" class="form-control" wire:model="semesterStartsOn">
                        @error('semesterStartsOn') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai Semester</label>
                        <input type="date" class="form-control" wire:model="semesterEndsOn">
                        @error('semesterEndsOn') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="semesterIsActive" wire:model="semesterIsActive">
                        <label for="semesterIsActive" style="margin-bottom: 0; cursor: pointer;">Jadikan sebagai Semester Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showSemesterModal', false)">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="saveSemester">Simpan Semester</button>
                </div>
            </div>
        </div>
    @endif
</div>
