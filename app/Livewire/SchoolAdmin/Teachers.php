<?php

namespace App\Livewire\SchoolAdmin;

use App\Models\ClassTeacher;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
#[Title('Kelola Guru & Penugasan — AMALIN')]
class Teachers extends Component
{
    public bool $showCreateModal = false;

    public bool $showAssignModal = false;

    // Create Teacher
    public string $name = '';

    public string $email = '';

    public string $password = 'password';

    // Assign Teacher
    public ?int $selectedTeacherId = null;

    public ?int $selectedClassId = null;

    public ?int $selectedSemesterId = null;

    public ?string $message = null;

    public function openCreate(): void
    {
        $this->reset(['name', 'email']);
        $this->password = 'password';
        $this->showCreateModal = true;
    }

    public function saveTeacher(): void
    {
        $school = Auth::user()->school;

        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);

        $user = User::create([
            'school_id' => $school->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);
        $user->syncRoles([$teacherRole]);

        $this->showCreateModal = false;
        $this->message = "Akun guru {$user->name} berhasil didaftarkan.";
    }

    public function openAssign(int $teacherId): void
    {
        $school = Auth::user()->school;
        $activeSemester = $school?->activeAcademicYear?->activeSemester;

        $this->selectedTeacherId = $teacherId;
        $this->selectedSemesterId = $activeSemester?->id;
        $this->selectedClassId = SchoolClass::where('school_id', $school->id)->first()?->id;
        $this->showAssignModal = true;
    }

    public function saveAssignment(): void
    {
        $this->validate([
            'selectedTeacherId' => 'required|exists:users,id',
            'selectedClassId' => 'required|exists:classes,id',
            'selectedSemesterId' => 'required|exists:semesters,id',
        ]);

        ClassTeacher::updateOrCreate(
            [
                'class_id' => $this->selectedClassId,
                'teacher_id' => $this->selectedTeacherId,
                'semester_id' => $this->selectedSemesterId,
            ],
            [
                'assigned_at' => now(),
                'unassigned_at' => null,
            ]
        );

        $this->showAssignModal = false;
        $this->message = 'Penugasan guru ke kelas berhasil disimpan.';
    }

    public function unassign(int $assignmentId): void
    {
        $assignment = ClassTeacher::findOrFail($assignmentId);
        $assignment->delete();
        $this->message = 'Penugasan guru berhasil dicabut.';
    }

    public function toggleTeacherActive(int $teacherId): void
    {
        $school = Auth::user()->school;
        $teacher = User::where('school_id', $school->id)->findOrFail($teacherId);
        $teacher->update(['is_active' => ! $teacher->is_active]);
        $status = $teacher->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->message = "Akun guru {$teacher->name} berhasil {$status}.";
    }

    public function render()
    {
        $school = Auth::user()->school;
        $activeYear = $school?->activeAcademicYear;
        $activeSemester = $activeYear?->activeSemester;

        $teachers = User::role('teacher')
            ->where('school_id', $school?->id)
            ->with(['teachingAssignments.schoolClass', 'teachingAssignments.semester'])
            ->get();

        $classes = SchoolClass::where('school_id', $school?->id)
            ->when($activeYear, fn ($q) => $q->where('academic_year_id', $activeYear->id))
            ->get();

        $semesters = Semester::whereHas('academicYear', fn ($q) => $q->where('school_id', $school?->id))->get();

        return view('livewire.school-admin.teachers', [
            'teachers' => $teachers,
            'classes' => $classes,
            'semesters' => $semesters,
            'activeSemester' => $activeSemester,
        ]);
    }
}
