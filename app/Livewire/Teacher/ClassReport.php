<?php

namespace App\Livewire\Teacher;

use App\Models\ActivityItem;
use App\Models\SchoolClass;
use App\Models\StudentActivityLog;
use App\Models\StudentPoint;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Laporan Kelas — AMALIN')]
class ClassReport extends Component
{
    public ?int $selectedClassId = null;

    public string $startDate = '';

    public string $endDate = '';

    public function mount(?int $class_id = null): void
    {
        $teacher = Auth::user();
        $classes = $teacher->currentTeachingClasses();
        $this->selectedClassId = $class_id ?? $classes->first()?->id;

        $this->startDate = Carbon::today()->startOfMonth()->toDateString();
        $this->endDate = Carbon::today()->toDateString();
    }

    public function render()
    {
        $teacher = Auth::user();
        $classes = $teacher->currentTeachingClasses();

        $currentClass = $this->selectedClassId
            ? SchoolClass::with('classStudents.student')->find($this->selectedClassId)
            : null;

        $students = $currentClass?->classStudents?->whereNull('left_at')->pluck('student')->filter() ?? collect();

        // Sholat activity item IDs (e.g. Subuh, Dzuhur, Ashar, Maghrib, Isya)
        $sholatItemIds = ActivityItem::where('name', 'like', '%Shalat%')
            ->orWhere('name', 'like', '%Sholat%')
            ->pluck('id')
            ->toArray();

        $reportData = [];
        $daysCount = max(1, Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) + 1);

        foreach ($students as $student) {
            // Logs in date range
            $logs = StudentActivityLog::where('student_id', $student->id)
                ->whereBetween('logged_on', [$this->startDate, $this->endDate])
                ->get();

            $totalFilled = $logs->filter(fn ($l) => $l->value === '1' || ($l->value && $l->value !== '0'))->count();

            // Sholat percentage
            $sholatCount = $logs->whereIn('activity_item_id', $sholatItemIds)
                ->filter(fn ($l) => $l->value === '1')
                ->count();
            $expectedSholat = count($sholatItemIds) * $daysCount;
            $sholatPercent = $expectedSholat > 0 ? min(100, (int) round(($sholatCount / $expectedSholat) * 100)) : 0;

            // Points in range
            $pointsInRange = StudentPoint::where('student_id', $student->id)
                ->whereBetween('credited_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
                ->sum('points');

            $reportData[] = [
                'student' => $student,
                'total_filled' => $totalFilled,
                'sholat_percent' => $sholatPercent,
                'points_range' => $pointsInRange,
                'total_points' => $student->totalVerifiedPoints(),
                'rank' => $student->classRank(),
            ];
        }

        // Sort by rank / points
        usort($reportData, fn ($a, $b) => $b['total_points'] <=> $a['total_points']);

        return view('livewire.teacher.class-report', [
            'classes' => $classes,
            'currentClass' => $currentClass,
            'reportData' => $reportData,
            'daysCount' => $daysCount,
        ]);
    }
}
