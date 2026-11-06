<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;

class ChangePassword extends Component
{
    public bool $showModal = false;

    public string $currentPassword = '';

    public string $newPassword = '';

    public string $newPasswordConfirmation = '';

    public ?string $successMessage = null;

    #[On('open-change-password')]
    public function openModal(): void
    {
        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation', 'successMessage']);
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation', 'successMessage']);
        $this->resetErrorBag();
    }

    public function updatePassword(): void
    {
        $this->successMessage = null;

        $this->validate([
            'currentPassword' => ['required', 'string'],
            'newPassword' => ['required', 'string', 'min:6'],
            'newPasswordConfirmation' => ['required', 'string', 'same:newPassword'],
        ], [
            'currentPassword.required' => 'Kata sandi saat ini wajib diisi.',
            'newPassword.required' => 'Kata sandi baru wajib diisi.',
            'newPassword.min' => 'Kata sandi baru minimal 6 karakter.',
            'newPasswordConfirmation.required' => 'Konfirmasi kata sandi baru wajib diisi.',
            'newPasswordConfirmation.same' => 'Konfirmasi kata sandi baru tidak sesuai.',
        ]);

        $userId = Auth::id();
        $user = User::findOrFail($userId);

        if (! Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'Kata sandi saat ini salah.');

            return;
        }

        if (Hash::check($this->newPassword, $user->password)) {
            $this->addError('newPassword', 'Kata sandi baru tidak boleh sama dengan kata sandi saat ini.');

            return;
        }

        $hashedPassword = Hash::make($this->newPassword);

        // Update langsung ke database menggunakan query builder untuk memastikan tersimpan di DB
        User::where('id', $userId)->update([
            'password' => $hashedPassword,
        ]);

        // Refresh sesi pengguna saat ini
        $user->password = $hashedPassword;
        Auth::setUser($user);

        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation']);
        $this->successMessage = 'Kata sandi Anda berhasil diperbarui di database!';
    }

    public function render()
    {
        return view('livewire.auth.change-password');
    }
}
