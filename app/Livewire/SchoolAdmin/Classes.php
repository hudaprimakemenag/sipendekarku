<?php

namespace App\Livewire\SchoolAdmin;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kelola Kelas — SIPENDEKARKU')]
class Classes extends Component
{
    public bool $showModal = false;

    public ?int $editingClassId = null;

    public string $className = '';

    public ?int $gradeLevel = 5;

    public ?int $academicYearId = null;

    public bool $isActive = true;

    public ?string $message = null;

    public function openCreate(): void
    {
        $school = Auth::user()->school;
        $activeYear = $school?->activeAcademicYear;

        $this->reset(['editingClassId', 'className']);
        $this->gradeLevel = 5;
        $this->academicYearId = $activeYear?->id;
        $this->isActive = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $class = SchoolClass::findOrFail($id);
        $this->editingClassId = $class->id;
        $this->className = $class->name;
        $this->gradeLevel = $class->grade_level;
        $this->academicYearId = $class->academic_year_id;
        $this->isActive = $class->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $school = Auth::user()->school;

        $this->validate([
            'className' => 'required|string|max:50',
            'gradeLevel' => 'required|integer|min:1|max:12',
            'academicYearId' => 'required|exists:academic_years,id',
        ]);

        SchoolClass::updateOrCreate(
            ['id' => $this->editingClassId],
            [
                'school_id' => $school->id,
                'academic_year_id' => $this->academicYearId,
                'name' => $this->className,
                'grade_level' => $this->gradeLevel,
                'is_active' => $this->isActive,
            ]
        );

        $this->showModal = false;
        $this->message = 'Data kelas berhasil disimpan.';
    }

    public function delete(int $id): void
    {
        $class = SchoolClass::findOrFail($id);
        $class->delete();
        $this->message = "Kelas {$class->name} berhasil dihapus.";
    }

    public function render()
    {
        $school = Auth::user()->school;
        $activeYear = $school?->activeAcademicYear;

        $classes = SchoolClass::where('school_id', $school?->id)
            ->with(['academicYear', 'classStudents.student', 'classTeachers.teacher'])
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $years = AcademicYear::where('school_id', $school?->id)->orderByDesc('starts_on')->get();

        return view('livewire.school-admin.classes', [
            'classes' => $classes,
            'years' => $years,
            'activeYear' => $activeYear,
        ]);
    }
}
