<?php

namespace App\Livewire\SchoolAdmin;

use App\Models\SchoolClass;
use App\Models\StudentPoint;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Laporan & Ranking Sekolah — SIPENDEKARKU')]
class Reports extends Component
{
    public string $selectedClassId = 'all';

    public function render()
    {
        $school = Auth::user()->school;
        $academicYear = $school?->activeAcademicYear;
        $semester = $academicYear?->activeSemester;

        $classes = SchoolClass::where('school_id', $school?->id)
            ->when($academicYear, fn ($q) => $q->where('academic_year_id', $academicYear->id))
            ->with(['classStudents.student'])
            ->get();

        // Class summaries
        $classSummaries = [];
        foreach ($classes as $c) {
            $studentIds = $c->classStudents->whereNull('left_at')->pluck('student_id');
            $pointsSum = StudentPoint::whereIn('student_id', $studentIds)
                ->when($semester, fn ($q) => $q->where('semester_id', $semester->id))
                ->sum('points');

            $count = $studentIds->count();
            $avgPoints = $count > 0 ? (int) round($pointsSum / $count) : 0;

            $classSummaries[] = [
                'class' => $c,
                'student_count' => $count,
                'total_points' => $pointsSum,
                'avg_points' => $avgPoints,
            ];
        }

        // Leaderboard query
        $studentQuery = User::role('student')
            ->where('school_id', $school?->id)
            ->with(['classAssignments.schoolClass']);

        if ($this->selectedClassId !== 'all') {
            $studentQuery->whereHas('classAssignments', function ($q) {
                $q->where('class_id', (int) $this->selectedClassId)->whereNull('left_at');
            });
        }

        $allStudents = $studentQuery->get();

        $rankedStudents = [];
        foreach ($allStudents as $st) {
            $pts = $st->totalVerifiedPoints($semester);
            $rankedStudents[] = [
                'student' => $st,
                'class' => $st->currentClass($semester),
                'total_points' => $pts,
            ];
        }

        usort($rankedStudents, fn ($a, $b) => $b['total_points'] <=> $a['total_points']);

        return view('livewire.school-admin.reports', [
            'classes' => $classes,
            'classSummaries' => $classSummaries,
            'rankedStudents' => $rankedStudents,
            'semester' => $semester,
            'academicYear' => $academicYear,
        ]);
    }
}
