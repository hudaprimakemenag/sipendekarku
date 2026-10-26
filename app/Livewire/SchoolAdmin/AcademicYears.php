<?php

namespace App\Livewire\SchoolAdmin;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Tahun Ajaran & Semester — SIPENDEKARKU')]
class AcademicYears extends Component
{
    public bool $showYearModal = false;

    public bool $showSemesterModal = false;

    public ?int $editingYearId = null;

    public string $yearName = '';

    public string $yearStartsOn = '';

    public string $yearEndsOn = '';

    public bool $yearIsActive = true;

    public ?int $selectedYearId = null;

    public ?int $editingSemesterId = null;

    public string $semesterName = 'Ganjil';

    public string $semesterStartsOn = '';

    public string $semesterEndsOn = '';

    public bool $semesterIsActive = true;

    public ?string $message = null;

    public function openCreateYear(): void
    {
        $this->reset(['editingYearId', 'yearName', 'yearStartsOn', 'yearEndsOn', 'yearIsActive']);
        $this->yearStartsOn = now()->format('Y-07-15');
        $this->yearEndsOn = now()->addYear()->format('Y-06-20');
        $this->showYearModal = true;
    }

    public function openEditYear(int $id): void
    {
        $year = AcademicYear::findOrFail($id);
        $this->editingYearId = $year->id;
        $this->yearName = $year->name;
        $this->yearStartsOn = $year->starts_on->format('Y-m-d');
        $this->yearEndsOn = $year->ends_on->format('Y-m-d');
        $this->yearIsActive = $year->is_active;
        $this->showYearModal = true;
    }

    public function saveYear(): void
    {
        $this->validate([
            'yearName' => 'required|string|max:50',
            'yearStartsOn' => 'required|date',
            'yearEndsOn' => 'required|date|after:yearStartsOn',
        ]);

        $school = Auth::user()->school;

        if ($this->yearIsActive) {
            AcademicYear::where('school_id', $school->id)->update(['is_active' => false]);
        }

        AcademicYear::updateOrCreate(
            ['id' => $this->editingYearId],
            [
                'school_id' => $school->id,
                'name' => $this->yearName,
                'starts_on' => $this->yearStartsOn,
                'ends_on' => $this->yearEndsOn,
                'is_active' => $this->yearIsActive,
            ]
        );

        $this->showYearModal = false;
        $this->message = 'Tahun ajaran berhasil disimpan.';
    }

    public function openCreateSemester(int $yearId): void
    {
        $this->selectedYearId = $yearId;
        $this->reset(['editingSemesterId', 'semesterName', 'semesterStartsOn', 'semesterEndsOn', 'semesterIsActive']);
        $this->semesterStartsOn = now()->format('Y-07-15');
        $this->semesterEndsOn = now()->format('Y-12-20');
        $this->showSemesterModal = true;
    }

    public function saveSemester(): void
    {
        $this->validate([
            'selectedYearId' => 'required|exists:academic_years,id',
            'semesterName' => 'required|string|max:50',
            'semesterStartsOn' => 'required|date',
            'semesterEndsOn' => 'required|date|after:semesterStartsOn',
        ]);

        if ($this->semesterIsActive) {
            Semester::where('academic_year_id', $this->selectedYearId)->update(['is_active' => false]);
        }

        Semester::updateOrCreate(
            ['id' => $this->editingSemesterId],
            [
                'academic_year_id' => $this->selectedYearId,
                'name' => $this->semesterName,
                'starts_on' => $this->semesterStartsOn,
                'ends_on' => $this->semesterEndsOn,
                'is_active' => $this->semesterIsActive,
            ]
        );

        $this->showSemesterModal = false;
        $this->message = 'Semester berhasil disimpan.';
    }

    public function toggleYearActive(int $id): void
    {
        $school = Auth::user()->school;
        $year = AcademicYear::where('school_id', $school->id)->findOrFail($id);

        AcademicYear::where('school_id', $school->id)->update(['is_active' => false]);
        $year->update(['is_active' => true]);

        $this->message = "Tahun ajaran {$year->name} telah diaktifkan.";
    }

    public function toggleSemesterActive(int $id): void
    {
        $semester = Semester::findOrFail($id);
        Semester::where('academic_year_id', $semester->academic_year_id)->update(['is_active' => false]);
        $semester->update(['is_active' => true]);

        $this->message = "Semester {$semester->name} telah diaktifkan.";
    }

    public function render()
    {
        $school = Auth::user()->school;
        $years = AcademicYear::where('school_id', $school?->id)
            ->with('semesters')
            ->orderByDesc('starts_on')
            ->get();

        return view('livewire.school-admin.academic-years', [
            'years' => $years,
        ]);
    }
}
