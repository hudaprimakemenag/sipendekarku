<div>
    <div class="panel-header">
        <div>
            <p class="eyebrow">STANDAR KEGIATAN GLOBAL</p>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Katalog Kelompok & Jenis Kegiatan</h1>
            <p style="color: var(--slate-500); font-size: 13px;">Kelola master kategori (Ibadah, Akhlak, Sosial), jenis kegiatan, tipe input (checklist/teks), dan bobot poin.</p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" class="btn btn-secondary" wire:click="openCreateGroup">
                + Tambah Kelompok (Group)
            </button>
            <button type="button" class="btn btn-primary" wire:click="openCreateItem">
                + Tambah Jenis Kegiatan (Item)
            </button>
        </div>
    </div>

    @if ($message)
        <div class="alert-box alert-success">
            ✓ {{ $message }}
        </div>
    @endif

    <div style="display: flex; flex-direction: column; gap: 24px;">
        @forelse ($groups as $grp)
            <div class="panel" style="margin-bottom: 0;">
                <div class="panel-header" style="border-bottom: 1px solid var(--line); padding-bottom: 14px; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span class="badge badge-success" style="font-size: 13px; padding: 4px 10px;">
                            Kelompok: {{ $grp->name }}
                        </span>
                        <span style="font-size: 12px; color: var(--slate-400);">Urutan: #{{ $grp->sort_order }}</span>
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="openEditGroup({{ $grp->id }})">
                            Edit Kelompok
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" wire:click="openCreateItem({{ $grp->id }})">
                            + Tambah Item di Kelompok Ini
                        </button>
                    </div>
                </div>

                <div class="table-responsive" style="border: none;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Kegiatan</th>
                                <th>Tipe Input</th>
                                <th>Bobot Poin</th>
                                <th>Urutan Tampil</th>
                                <th>Status</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($grp->activityItems as $item)
                                <tr>
                                    <td>
                                        <span class="badge badge-neutral" style="font-family: monospace;">{{ $item->code }}</span>
                                    </td>
                                    <td>
                                        <strong style="color: var(--dark);">{{ $item->name }}</strong>
                                    </td>
                                    <td>
                                        @if ($item->input_type === 'checklist')
                                            <span class="badge badge-success">✓ Checklist (Ya/Tidak)</span>
                                        @else
                                            <span class="badge badge-warning">✎ Input Teks / Angka</span>
                                        @endif
                                    </td>
                                    <td>
                                        <b style="color: var(--emerald-dark);">+{{ $item->point_weight }} Poin</b>
                                    </td>
                                    <td>
                                        <span style="color: var(--slate-500); font-size: 12px;">#{{ $item->sort_order }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-neutral' }}">
                                            {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="display: inline-flex; gap: 6px;">
                                            <button type="button" class="btn btn-sm btn-secondary" wire:click="openEditItem({{ $item->id }})">
                                                Edit
                                            </button>
                                            <button type="button" class="btn btn-sm {{ $item->is_active ? 'btn-danger' : 'btn-primary' }}" wire:click="toggleItemActive({{ $item->id }})">
                                                {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; color: var(--slate-400); padding: 16px;">
                                        Belum ada item kegiatan di kelompok ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="panel" style="text-align: center; padding: 48px; color: var(--slate-400);">
                Belum ada kelompok kegiatan yang dibuat. Klik "+ Tambah Kelompok" untuk membuat baru.
            </div>
        @endforelse
    </div>

    <!-- Group Modal -->
    @if ($showGroupModal)
        <div class="modal-overlay">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">{{ $editingGroupId ? 'Edit Kelompok Kegiatan' : 'Tambah Kelompok Kegiatan Baru' }}</h3>
                    <button type="button" class="sidebar-close-btn" style="display:grid;" wire:click="$set('showGroupModal', false)">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kelompok</label>
                        <input type="text" class="form-control" placeholder="Contoh: Ibadah, Akhlak, Sosial" wire:model="groupName">
                        @error('groupName') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Urutan Tampil (Sort Order)</label>
                        <input type="number" min="0" class="form-control" wire:model="groupSortOrder">
                        @error('groupSortOrder') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="groupIsActive" wire:model="groupIsActive">
                        <label for="groupIsActive" style="margin-bottom: 0; cursor: pointer;">Kelompok Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showGroupModal', false)">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="saveGroup">Simpan Kelompok</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Item Modal -->
    @if ($showItemModal)
        <div class="modal-overlay">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">{{ $editingItemId ? 'Edit Jenis Kegiatan' : 'Tambah Jenis Kegiatan Baru' }}</h3>
                    <button type="button" class="sidebar-close-btn" style="display:grid;" wire:click="$set('showItemModal', false)">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kelompok Kegiatan</label>
                        <select class="form-control" wire:model="itemGroupId">
                            @foreach ($groups as $g)
                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                        @error('itemGroupId') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Kode Unik Kegiatan</label>
                        <input type="text" class="form-control" placeholder="Contoh: IBD-001, AKH-001" wire:model="itemCode">
                        @error('itemCode') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Nama Kegiatan</label>
                        <input type="text" class="form-control" placeholder="Contoh: Shalat Subuh Berjamaah" wire:model="itemName">
                        @error('itemName') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Tipe Input Laporan</label>
                        <select class="form-control" wire:model="itemInputType">
                            <option value="checklist">Checklist (Ya / Tidak)</option>
                            <option value="text">Teks / Angka (Jumlah ayat, nominal rupiah, dsb)</option>
                        </select>
                        @error('itemInputType') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Bobot Poin (Poin yang diperoleh saat disetujui)</label>
                        <input type="number" min="0" class="form-control" wire:model="itemPointWeight">
                        @error('itemPointWeight') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Urutan Tampil (Sort Order)</label>
                        <input type="number" min="0" class="form-control" wire:model="itemSortOrder">
                        @error('itemSortOrder') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="itemIsActive" wire:model="itemIsActive">
                        <label for="itemIsActive" style="margin-bottom: 0; cursor: pointer;">Jenis Kegiatan Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showItemModal', false)">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="saveItem">Simpan Kegiatan</button>
                </div>
            </div>
        </div>
    @endif
</div>
