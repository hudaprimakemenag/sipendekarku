<?php

namespace App\Livewire\Teacher;

use App\Models\ClassStudent;
use App\Models\ClassTeacher;
use App\Models\StudentActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Guru — AMALIN')]
class Dashboard extends Component
{
    public function render()
    {
        $teacher = Auth::user();
        $school = $teacher->school;
        $academicYear = $school?->activeAcademicYear;
        $semester = $academicYear?->activeSemester;

        // Teaching classes
        $assignedClasses = ClassTeacher::where('teacher_id', $teacher->id)
            ->whereNull('unassigned_at')
            ->when($semester, fn ($q) => $q->where('semester_id', $semester->id))
            ->with(['schoolClass.classStudents.student'])
            ->get();

        $classIds = $assignedClasses->pluck('class_id')->toArray();

        // Pending verifications (logs without verification or verification status = 'pending')
        $pendingTodayCount = StudentActivityLog::whereIn('class_id', $classIds)
            ->whereDate('logged_on', Carbon::today())
            ->where(function ($q) {
                $q->where('value', '1')->orWhere('value', '!=', '');
            })
            ->whereDoesntHave('verification', fn ($v) => $v->where('status', 'approved'))
            ->count();

        $pendingYesterdayCount = StudentActivityLog::whereIn('class_id', $classIds)
            ->whereDate('logged_on', Carbon::yesterday())
            ->where(function ($q) {
                $q->where('value', '1')->orWhere('value', '!=', '');
            })
            ->whereDoesntHave('verification', fn ($v) => $v->where('status', 'approved'))
            ->count();

        $totalVerifiedWeek = StudentActivityLog::whereIn('class_id', $classIds)
            ->whereBetween('logged_on', [Carbon::today()->startOfWeek(), Carbon::today()->endOfWeek()])
            ->whereHas('verification', fn ($v) => $v->where('status', 'approved'))
            ->count();

        $totalStudents = ClassStudent::whereIn('class_id', $classIds)
            ->whereNull('left_at')
            ->distinct('student_id')
            ->count('student_id');

        return view('livewire.teacher.dashboard', [
            'teacher' => $teacher,
            'school' => $school,
            'semester' => $semester,
            'assignedClasses' => $assignedClasses,
            'pendingTodayCount' => $pendingTodayCount,
            'pendingYesterdayCount' => $pendingYesterdayCount,
            'totalVerifiedWeek' => $totalVerifiedWeek,
            'totalStudents' => $totalStudents,
        ]);
    }
}
