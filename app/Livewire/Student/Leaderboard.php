<?php

namespace App\Livewire\Student;

use App\Models\ClassStudent;
use App\Models\StudentPoint;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Poin & Leaderboard — SIPENDEKARKU')]
class Leaderboard extends Component
{
    use WithPagination;

    public function render()
    {
        $user = Auth::user();
        $school = $user->school;
        $academicYear = $school?->activeAcademicYear;
        $semester = $academicYear?->activeSemester;
        $class = $user->currentClass($semester);

        $leaderboard = [];
        if ($class) {
            $studentIds = ClassStudent::where('class_id', $class->id)
                ->when($semester, fn ($q) => $q->where('semester_id', $semester->id))
                ->whereNull('left_at')
                ->pluck('student_id');

            $scores = StudentPoint::whereIn('student_id', $studentIds)
                ->when($semester, fn ($q) => $q->where('semester_id', $semester->id))
                ->selectRaw('student_id, SUM(points) as total_points, COUNT(id) as total_activities')
                ->groupBy('student_id')
                ->with('student')
                ->orderByDesc('total_points')
                ->get();

            // Include all class students even if 0 points
            $scoredUserIds = $scores->pluck('student_id')->toArray();
            $missingStudents = User::whereIn('id', $studentIds)
                ->whereNotIn('id', $scoredUserIds)
                ->get();

            $rank = 1;
            foreach ($scores as $s) {
                if ($s->student) {
                    $leaderboard[] = [
                        'rank' => $rank++,
                        'student' => $s->student,
                        'total_points' => (int) $s->total_points,
                        'total_activities' => (int) $s->total_activities,
                        'is_me' => $s->student->id === $user->id,
                    ];
                }
            }
            foreach ($missingStudents as $st) {
                $leaderboard[] = [
                    'rank' => $rank++,
                    'student' => $st,
                    'total_points' => 0,
                    'total_activities' => 0,
                    'is_me' => $st->id === $user->id,
                ];
            }
        }

        $myPointLogs = StudentPoint::where('student_id', $user->id)
            ->with(['verification.studentActivityLog.activityItem', 'verification.teacher'])
            ->orderByDesc('credited_at')
            ->paginate(15);

        return view('livewire.student.leaderboard', [
            'class' => $class,
            'semester' => $semester,
            'leaderboard' => $leaderboard,
            'myPointLogs' => $myPointLogs,
        ]);
    }
}
