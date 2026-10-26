<?php

namespace App\Livewire\SchoolAdmin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
#[Title('Wali Murid & Relasi Siswa — AMALIN')]
class Parents extends Component
{
    public bool $showCreateModal = false;

    public bool $showLinkModal = false;

    // Create Parent
    public string $name = '';

    public string $email = '';

    public string $password = 'password';

    // Link Parent to Student
    public ?int $selectedParentId = null;

    public ?int $selectedStudentId = null;

    public ?string $message = null;

    public function openCreate(): void
    {
        $this->reset(['name', 'email']);
        $this->password = 'password';
        $this->showCreateModal = true;
    }

    public function saveParent(): void
    {
        $school = Auth::user()->school;

        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $parentRole = Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);

        $user = User::create([
            'school_id' => $school->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);
        $user->syncRoles([$parentRole]);

        $this->showCreateModal = false;
        $this->message = "Akun wali murid {$user->name} berhasil didaftarkan.";
    }

    public function openLink(int $parentId): void
    {
        $this->selectedParentId = $parentId;
        $this->selectedStudentId = null;
        $this->showLinkModal = true;
    }

    public function linkStudent(): void
    {
        $this->validate([
            'selectedParentId' => 'required|exists:users,id',
            'selectedStudentId' => 'required|exists:users,id',
        ]);

        DB::table('parent_student')->insertOrIgnore([
            'parent_id' => $this->selectedParentId,
            'student_id' => $this->selectedStudentId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->showLinkModal = false;
        $this->message = 'Siswa berhasil dihubungkan ke akun wali murid.';
    }

    public function unlinkStudent(int $parentId, int $studentId): void
    {
        DB::table('parent_student')
            ->where('parent_id', $parentId)
            ->where('student_id', $studentId)
            ->delete();

        $this->message = 'Tautan siswa berhasil dilepas dari wali murid.';
    }

    public function render()
    {
        $school = Auth::user()->school;

        $parents = User::role('parent')
            ->where('school_id', $school?->id)
            ->with(['children.classAssignments.schoolClass'])
            ->get();

        $students = User::role('student')
            ->where('school_id', $school?->id)
            ->orderBy('name')
            ->get();

        return view('livewire.school-admin.parents', [
            'parents' => $parents,
            'students' => $students,
        ]);
    }
}
