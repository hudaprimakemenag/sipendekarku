<?php

namespace App\Livewire\SchoolAdmin;

use App\Models\SchoolClass;
use App\Models\StudentActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Admin Sekolah — AMALIN')]
class Dashboard extends Component
{
    public function render()
    {
        $admin = Auth::user();
        $school = $admin->school;
        $academicYear = $school?->activeAcademicYear;
        $semester = $academicYear?->activeSemester;

        $totalClasses = SchoolClass::where('school_id', $school?->id)->count();

        $totalStudents = User::role('student')
            ->where('school_id', $school?->id)
            ->count();

        $totalTeachers = User::role('teacher')
            ->where('school_id', $school?->id)
            ->count();

        $totalParents = User::role('parent')
            ->where('school_id', $school?->id)
            ->count();

        $todayLogsCount = StudentActivityLog::where('school_id', $school?->id)
            ->whereDate('logged_on', Carbon::today())
            ->where(function ($q) {
                $q->where('value', '1')->orWhere('value', '!=', '');
            })
            ->count();

        $activeStudentsToday = StudentActivityLog::where('school_id', $school?->id)
            ->whereDate('logged_on', Carbon::today())
            ->where(function ($q) {
                $q->where('value', '1')->orWhere('value', '!=', '');
            })
            ->distinct('student_id')
            ->count('student_id');

        $activePercent = $totalStudents > 0 ? min(100, (int) round(($activeStudentsToday / $totalStudents) * 100)) : 0;

        $classes = SchoolClass::where('school_id', $school?->id)
            ->when($academicYear, fn ($q) => $q->where('academic_year_id', $academicYear->id))
            ->with(['classStudents.student', 'classTeachers.teacher'])
            ->get();

        return view('livewire.school-admin.dashboard', [
            'school' => $school,
            'academicYear' => $academicYear,
            'semester' => $semester,
            'totalClasses' => $totalClasses,
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalParents' => $totalParents,
            'todayLogsCount' => $todayLogsCount,
            'activeStudentsToday' => $activeStudentsToday,
            'activePercent' => $activePercent,
            'classes' => $classes,
        ]);
    }
}
