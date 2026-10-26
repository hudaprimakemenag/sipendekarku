<?php

namespace App\Livewire\SchoolAdmin;

use App\Models\ClassStudent;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
#[Title('Kelola Siswa & Penempatan — SIPENDEKARKU')]
class Students extends Component
{
    use WithPagination;

    public bool $showCreateModal = false;

    public bool $showAssignModal = false;

    public string $search = '';

    public string $filterClass = 'all';

    public string $filterStatus = 'active';

    // Create Student
    public string $name = '';

    public string $email = '';

    public string $studentNumber = '';

    public ?int $initialClassId = null;

    public string $password = 'password';

    // Assign / Move Student
    public ?int $selectedStudentId = null;

    public ?int $selectedClassId = null;

    public ?int $selectedSemesterId = null;

    public ?string $message = null;

    public function openCreate(): void
    {
        $school = Auth::user()->school;
        $this->reset(['name', 'email', 'studentNumber']);
        $this->initialClassId = SchoolClass::where('school_id', $school->id)->first()?->id;
        $this->password = 'password';
        $this->showCreateModal = true;
    }

    public function saveStudent(): void
    {
        $school = Auth::user()->school;
        $activeSemester = $school?->activeAcademicYear?->activeSemester;

        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'studentNumber' => 'nullable|string|max:30',
            'password' => 'required|string|min:6',
        ]);

        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $user = User::create([
            'school_id' => $school->id,
            'name' => $this->name,
            'email' => $this->email,
            'student_number' => $this->studentNumber,
            'password' => Hash::make($this->password),
        ]);
        $user->syncRoles([$studentRole]);

        if ($this->initialClassId && $activeSemester) {
            ClassStudent::create([
                'class_id' => $this->initialClassId,
                'student_id' => $user->id,
                'semester_id' => $activeSemester->id,
                'assigned_at' => now(),
            ]);
        }

        $this->showCreateModal = false;
        $this->message = "Siswa {$user->name} berhasil ditambahkan.";
    }

    public function openAssign(int $studentId): void
    {
        $school = Auth::user()->school;
        $activeSemester = $school?->activeAcademicYear?->activeSemester;

        $this->selectedStudentId = $studentId;
        $this->selectedSemesterId = $activeSemester?->id;
        $this->selectedClassId = SchoolClass::where('school_id', $school->id)->first()?->id;
        $this->showAssignModal = true;
    }

    public function saveAssignment(): void
    {
        $this->validate([
            'selectedStudentId' => 'required|exists:users,id',
            'selectedClassId' => 'required|exists:classes,id',
            'selectedSemesterId' => 'required|exists:semesters,id',
        ]);

        ClassStudent::updateOrCreate(
            [
                'student_id' => $this->selectedStudentId,
                'semester_id' => $this->selectedSemesterId,
            ],
            [
                'class_id' => $this->selectedClassId,
                'assigned_at' => now(),
                'left_at' => null,
            ]
        );

        $this->showAssignModal = false;
        $this->message = 'Penempatan kelas siswa berhasil diperbarui.';
    }

    public function graduateStudent(int $studentId): void
    {
        $school = Auth::user()->school;
        $student = User::where('school_id', $school->id)->findOrFail($studentId);
        $student->update(['graduated_at' => now()]);

        // Close all active class assignments
        $student->classAssignments()->whereNull('left_at')->update(['left_at' => now()]);

        $this->message = "Siswa {$student->name} berhasil ditandai sebagai lulus.";
    }

    public function ungraduateStudent(int $studentId): void
    {
        $school = Auth::user()->school;
        $student = User::where('school_id', $school->id)->findOrFail($studentId);
        $student->update(['graduated_at' => null]);
        $this->message = "Status kelulusan siswa {$student->name} berhasil dibatalkan.";
    }

    public function render()
    {
        $school = Auth::user()->school;
        $activeYear = $school?->activeAcademicYear;
        $activeSemester = $activeYear?->activeSemester;

        $query = User::role('student')
            ->where('school_id', $school?->id)
            ->with(['classAssignments.schoolClass', 'classAssignments.semester'])
            ->orderBy('name');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('student_number', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->filterClass !== 'all') {
            $query->whereHas('classAssignments', function ($q) {
                $q->where('class_id', $this->filterClass)->whereNull('left_at');
            });
        }

        $students = $query->paginate(15);

        $classes = SchoolClass::where('school_id', $school?->id)
            ->when($activeYear, fn ($q) => $q->where('academic_year_id', $activeYear->id))
            ->get();

        $semesters = Semester::whereHas('academicYear', fn ($q) => $q->where('school_id', $school?->id))->get();

        return view('livewire.school-admin.students', [
            'students' => $students,
            'classes' => $classes,
            'semesters' => $semesters,
            'activeSemester' => $activeSemester,
        ]);
    }
}
