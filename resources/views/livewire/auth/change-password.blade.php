<div wire:key="change-password-modal-root" @open-change-password.window="$wire.openModal()">
    @if ($showModal)
        <div class="modal-overlay" style="z-index: 9999;" wire:keydown.escape="closeModal" @click.self="$wire.closeModal()">
            <div class="modal-dialog" style="max-width: 440px;" x-data="{ showOld: false, showNew: false, showConfirm: false }">
                <div class="modal-header">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 20px;">🔑</span>
                        <div>
                            <h3 class="modal-title" style="margin: 0;">Ubah Kata Sandi</h3>
                            <small style="font-size: 11px; color: var(--slate-400);">Perbarui kata sandi akun Anda</small>
                        </div>
                    </div>
                    <button type="button" class="sidebar-close-btn" style="display: grid;" wire:click="closeModal" aria-label="Tutup modal">✕</button>
                </div>

                <form wire:submit.prevent="updatePassword">
                    <div class="modal-body">
                        @if ($successMessage)
                            <div class="alert-box alert-success" style="margin-bottom: 18px;">
                                <span>✓</span>
                                <span>{{ $successMessage }}</span>
                            </div>
                        @endif

                        {{-- Current Password --}}
                        <div class="form-group">
                            <label for="currentPassword">Kata Sandi Saat Ini</label>
                            <div style="position: relative;">
                                <input 
                                    :type="showOld ? 'text' : 'password'" 
                                    id="currentPassword" 
                                    class="form-control @error('currentPassword') is-invalid @enderror" 
                                    placeholder="Masukkan kata sandi lama" 
                                    wire:model="currentPassword"
                                    autocomplete="current-password"
                                    style="padding-right: 40px;"
                                >
                                <button type="button" 
                                        @click="showOld = !showOld" 
                                        aria-label="Tampilkan kata sandi" 
                                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--slate-400); font-size: 15px; padding: 4px;">
                                    <span x-text="showOld ? '👁' : '👁‍🗨'"></span>
                                </button>
                            </div>
                            @error('currentPassword') <span class="form-error" style="color: var(--rose, #e11d48); font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        {{-- New Password --}}
                        <div class="form-group">
                            <label for="newPassword">Kata Sandi Baru</label>
                            <div style="position: relative;">
                                <input 
                                    :type="showNew ? 'text' : 'password'" 
                                    id="newPassword" 
                                    class="form-control @error('newPassword') is-invalid @enderror" 
                                    placeholder="Minimal 6 karakter" 
                                    wire:model="newPassword"
                                    autocomplete="new-password"
                                    style="padding-right: 40px;"
                                >
                                <button type="button" 
                                        @click="showNew = !showNew" 
                                        aria-label="Tampilkan kata sandi" 
                                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--slate-400); font-size: 15px; padding: 4px;">
                                    <span x-text="showNew ? '👁' : '👁‍🗨'"></span>
                                </button>
                            </div>
                            @error('newPassword') <span class="form-error" style="color: var(--rose, #e11d48); font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        {{-- Confirm New Password --}}
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="newPasswordConfirmation">Konfirmasi Kata Sandi Baru</label>
                            <div style="position: relative;">
                                <input 
                                    :type="showConfirm ? 'text' : 'password'" 
                                    id="newPasswordConfirmation" 
                                    class="form-control @error('newPasswordConfirmation') is-invalid @enderror" 
                                    placeholder="Ulangi kata sandi baru" 
                                    wire:model="newPasswordConfirmation"
                                    autocomplete="new-password"
                                    style="padding-right: 40px;"
                                >
                                <button type="button" 
                                        @click="showConfirm = !showConfirm" 
                                        aria-label="Tampilkan kata sandi" 
                                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--slate-400); font-size: 15px; padding: 4px;">
                                    <span x-text="showConfirm ? '👁' : '👁‍🗨'"></span>
                                </button>
                            </div>
                            @error('newPasswordConfirmation') <span class="form-error" style="color: var(--rose, #e11d48); font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="updatePassword">Simpan Kata Sandi</span>
                            <span wire:loading wire:target="updatePassword">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
