<?php

namespace App\Livewire\Superadmin;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
#[Title('Manajemen Pengguna — SIPENDEKARKU')]
class Users extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';

    public string $filterRole = '';

    public string $filterSchool = '';

    // Modal state
    public bool $showModal = false;

    public bool $showDeleteConfirm = false;

    public ?int $editingUserId = null;

    public ?int $deletingUserId = null;

    // Form fields
    public string $name = '';

    public string $email = '';

    public string $password = 'password';

    public string $role = 'school_admin';

    public ?int $schoolId = null;

    public string $phone = '';

    public string $studentNumber = '';

    public ?string $message = null;

    public string $messageType = 'success';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterRole(): void
    {
        $this->resetPage();
    }

    public function updatedFilterSchool(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset(['editingUserId', 'name', 'email', 'phone', 'studentNumber']);
        $this->password = 'password';
        $this->role = 'school_admin';
        $this->schoolId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->studentNumber = $user->student_number ?? '';
        $this->role = $user->getRoleNames()->first() ?? 'school_admin';
        $this->schoolId = $user->school_id;
        $this->password = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.($this->editingUserId ?? 'NULL'),
            'role' => 'required|in:school_admin,teacher,student,parent',
            'phone' => 'nullable|string|max:20',
            'studentNumber' => 'nullable|string|max:30',
        ];

        if (! $this->editingUserId) {
            $rules['password'] = 'required|string|min:6';
            $rules['schoolId'] = 'required|exists:schools,id';
        } else {
            $rules['password'] = 'nullable|string|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'student_number' => $this->studentNumber ?: null,
        ];

        if ($this->schoolId) {
            $data['school_id'] = $this->schoolId;
        }

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $user->update($data);
        } else {
            $user = User::create($data);
        }

        $roleModel = Role::firstOrCreate(['name' => $this->role, 'guard_name' => 'web']);
        $user->syncRoles([$roleModel]);

        $this->showModal = false;
        $this->message = $this->editingUserId
            ? "Pengguna {$user->name} berhasil diperbarui."
            : "Pengguna {$user->name} berhasil ditambahkan.";
        $this->messageType = 'success';
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingUserId = $id;
        $this->showDeleteConfirm = true;
    }

    public function deleteUser(): void
    {
        $user = User::findOrFail($this->deletingUserId);
        $name = $user->name;
        $user->delete();

        $this->showDeleteConfirm = false;
        $this->deletingUserId = null;
        $this->message = "Pengguna {$name} berhasil dihapus.";
        $this->messageType = 'danger';
    }

    public function resetPassword(int $id): void
    {
        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make('password')]);
        $this->message = "Password {$user->name} direset ke 'password'.";
        $this->messageType = 'success';
    }

    public function render()
    {
        $users = User::query()
            ->with('school')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('student_number', 'like', "%{$this->search}%");
            }))
            ->when($this->filterRole, fn ($q) => $q->role($this->filterRole))
            ->when($this->filterSchool, fn ($q) => $q->where('school_id', $this->filterSchool))
            ->orderBy('name')
            ->paginate(20);

        $schools = School::orderBy('name')->get();

        $roleCounts = [
            'school_admin' => User::role('school_admin')->count(),
            'teacher' => User::role('teacher')->count(),
            'student' => User::role('student')->count(),
            'parent' => User::role('parent')->count(),
        ];

        return view('livewire.superadmin.users', [
            'users' => $users,
            'schools' => $schools,
            'roleCounts' => $roleCounts,
        ]);
    }
}
