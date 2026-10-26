<?php

namespace App\Livewire\Parent;

use App\Models\ActivityGroup;
use App\Models\ActivityItem;
use App\Models\StudentActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Pemantauan Anak — SIPENDEKARKU')]
class Dashboard extends Component
{
    public ?int $selectedChildId = null;

    public string $timeframe = 'weekly'; // daily, weekly, monthly

    public string $selectedDate = '';

    public function mount(): void
    {
        $parent = Auth::user();
        $children = $parent->children;
        $this->selectedChildId = $children->first()?->id;
        $this->selectedDate = Carbon::today()->toDateString();
    }

    public function setChild(int $childId): void
    {
        $this->selectedChildId = $childId;
    }

    public function setTimeframe(string $tf): void
    {
        $this->timeframe = $tf;
    }

    public function render()
    {
        $parent = Auth::user();
        $children = $parent->children;

        $selectedChild = $this->selectedChildId ? User::find($this->selectedChildId) : $children->first();

        $sholatRate = 0;
        $totalActivities = 0;
        $verifiedPoints = 0;
        $classRank = 1;
        $childClass = null;
        $childSemester = null;

        $logs = collect();
        $recentNotes = collect();
        $weeklyBars = [];

        if ($selectedChild) {
            $school = $selectedChild->school;
            $academicYear = $school?->activeAcademicYear;
            $childSemester = $academicYear?->activeSemester;
            $childClass = $selectedChild->currentClass($childSemester);

            $verifiedPoints = $selectedChild->totalVerifiedPoints($childSemester);
            $classRank = $selectedChild->classRank($childSemester);

            // Determine date range based on timeframe
            [$startDate, $endDate] = match ($this->timeframe) {
                'daily' => [Carbon::parse($this->selectedDate)->startOfDay(), Carbon::parse($this->selectedDate)->endOfDay()],
                'monthly' => [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()],
                default => [Carbon::today()->startOfWeek(), Carbon::today()->endOfWeek()], // weekly
            };

            $logs = StudentActivityLog::where('student_id', $selectedChild->id)
                ->whereBetween('logged_on', [$startDate->toDateString(), $endDate->toDateString()])
                ->with(['activityItem.activityGroup', 'verification.teacher'])
                ->orderByDesc('logged_on')
                ->get();

            $totalActivities = $logs->filter(fn ($l) => $l->value === '1' || ($l->value && $l->value !== '0'))->count();

            // Sholat rate in range
            $sholatItemIds = ActivityItem::where('name', 'like', '%Shalat%')
                ->orWhere('name', 'like', '%Sholat%')
                ->pluck('id')
                ->toArray();

            $daysInRange = max(1, $startDate->diffInDays($endDate) + 1);
            $sholatDone = $logs->whereIn('activity_item_id', $sholatItemIds)->filter(fn ($l) => $l->value === '1')->count();
            $expectedSholat = count($sholatItemIds) * $daysInRange;
            $sholatRate = $expectedSholat > 0 ? min(100, (int) round(($sholatDone / $expectedSholat) * 100)) : 0;

            // Teacher notes
            $recentNotes = $logs->filter(fn ($l) => $l->verification && ! empty($l->verification->notes))->take(5);

            // 7-day progress bars for child
            for ($i = 6; $i >= 0; $i--) {
                $d = Carbon::today()->subDays($i);
                $dayCount = StudentActivityLog::where('student_id', $selectedChild->id)
                    ->whereDate('logged_on', $d->toDateString())
                    ->where(function ($q) {
                        $q->where('value', '1')->orWhere('value', '!=', '');
                    })
                    ->count();

                $dayPercent = min(100, (int) round(($dayCount / 8) * 100));
                $dayLabel = match ($d->dayOfWeekIso) {
                    1 => 'Sen',
                    2 => 'Sel',
                    3 => 'Rab',
                    4 => 'Kam',
                    5 => 'Jum',
                    6 => 'Sab',
                    default => 'Min',
                };

                $weeklyBars[] = [
                    'day' => $i === 0 ? 'Hari ini' : $dayLabel,
                    'percent' => $dayPercent,
                    'is_today' => $i === 0,
                ];
            }
        }

        $groups = ActivityGroup::where('is_active', true)->orderBy('sort_order')->get();

        return view('livewire.parent.dashboard', [
            'children' => $children,
            'selectedChild' => $selectedChild,
            'childClass' => $childClass,
            'childSemester' => $childSemester,
            'sholatRate' => $sholatRate,
            'totalActivities' => $totalActivities,
            'verifiedPoints' => $verifiedPoints,
            'classRank' => $classRank,
            'logs' => $logs,
            'recentNotes' => $recentNotes,
            'weeklyBars' => $weeklyBars,
            'groups' => $groups,
        ]);
    }
}
