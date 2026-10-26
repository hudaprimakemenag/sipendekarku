<?php

namespace App\Livewire\Superadmin;

use App\Models\School;
use App\Models\StudentActivityLog;
use App\Models\StudentPoint;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Laporan Lintas Sekolah — SIPENDEKARKU')]
class Reports extends Component
{
    public function render()
    {
        $schools = School::with(['users' => fn ($q) => $q->with('roles')])->get();

        $schoolSummaries = [];
        foreach ($schools as $sch) {
            $studentCount = User::role('student')->where('school_id', $sch->id)->count();
            $teacherCount = User::role('teacher')->where('school_id', $sch->id)->count();

            $totalLogs = StudentActivityLog::where('school_id', $sch->id)
                ->where(function ($q) {
                    $q->where('value', '1')->orWhere('value', '!=', '');
                })
                ->count();

            $totalPoints = StudentPoint::where('school_id', $sch->id)->sum('points');
            $avgPoints = $studentCount > 0 ? (int) round($totalPoints / $studentCount) : 0;

            $schoolSummaries[] = [
                'school' => $sch,
                'student_count' => $studentCount,
                'teacher_count' => $teacherCount,
                'total_logs' => $totalLogs,
                'total_points' => $totalPoints,
                'avg_points' => $avgPoints,
            ];
        }

        // Sort by total points
        usort($schoolSummaries, fn ($a, $b) => $b['total_points'] <=> $a['total_points']);

        return view('livewire.superadmin.reports', [
            'schoolSummaries' => $schoolSummaries,
        ]);
    }
}
