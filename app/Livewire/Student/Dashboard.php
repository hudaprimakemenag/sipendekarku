<?php

namespace App\Livewire\Student;

use App\Models\ActivityGroup;
use App\Models\ActivityItem;
use App\Models\ActivityVerification;
use App\Models\ClassStudent;
use App\Models\StudentActivityLog;
use App\Models\StudentPoint;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Mutaba\'ah Hari Ini — SIPENDEKARKU')]
class Dashboard extends Component
{
    public string $activeGroup = 'all';

    public bool $submitted = false;

    /** @var array<int, array{id: int, item_id: int, group_id: int, group_name: string, title: string, detail: string, input_type: string, point_weight: int, value: string, checked: bool}> */
    public array $activities = [];

    public int $verifiedPoints = 0;

    public int $classRank = 1;

    public int $totalClassStudents = 1;

    public int $streakDays = 1;

    public ?string $latestTeacherNote = null;

    public ?string $latestTeacherName = null;

    public ?string $latestTeacherDate = null;

    public array $recentWeeklyProgress = [];

    public array $topLeaderboard = [];

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $today = Carbon::today()->toDateString();
        $school = $user->school;
        $academicYear = $school?->activeAcademicYear;
        $semester = $academicYear?->activeSemester;
        $class = $user->currentClass($semester);

        // 1. Load active activity items
        $items = ActivityItem::where('is_active', true)
            ->with('activityGroup')
            ->orderBy('sort_order')
            ->get();

        // 2. Load today's existing logs
        $existingLogs = StudentActivityLog::where('student_id', $user->id)
            ->whereDate('logged_on', $today)
            ->get()
            ->keyBy('activity_item_id');

        $activityList = [];
        foreach ($items as $item) {
            $log = $existingLogs->get($item->id);
            $val = $log?->value ?? '';
            $isChecked = $item->input_type === 'checklist' ? ($val === '1' || $val === 'true') : (! empty($val));

            $activityList[] = [
                'id' => $log?->id ?? 0,
                'item_id' => $item->id,
                'group_id' => $item->activity_group_id,
                'group_name' => strtolower($item->activityGroup?->name ?? 'lainnya'),
                'title' => $item->name,
                'detail' => $item->input_type === 'checklist' ? 'Tandai jika dikerjakan hari ini' : 'Isi catatan atau jumlah capaian',
                'input_type' => $item->input_type,
                'point_weight' => $item->point_weight,
                'value' => $val,
                'checked' => $isChecked,
            ];
        }
        $this->activities = $activityList;

        // 3. Verified points & ranking
        $this->verifiedPoints = $user->totalVerifiedPoints($semester);
        $this->classRank = $user->classRank($semester);

        if ($class) {
            $this->totalClassStudents = ClassStudent::where('class_id', $class->id)
                ->when($semester, fn ($q) => $q->where('semester_id', $semester->id))
                ->whereNull('left_at')
                ->count();
        }

        // 4. Streak Calculation
        $this->calculateStreak($user->id);

        // 5. Latest teacher note
        $latestVerification = ActivityVerification::whereHas('studentActivityLog', function ($q) use ($user) {
            $q->where('student_id', $user->id);
        })
            ->whereNotNull('notes')
            ->where('notes', '!=', '')
            ->with('teacher')
            ->latest('verified_at')
            ->first();

        if ($latestVerification) {
            $this->latestTeacherNote = $latestVerification->notes;
            $this->latestTeacherName = $latestVerification->teacher?->name ?? 'Ustadz / Ustadzah';
            $this->latestTeacherDate = $latestVerification->verified_at ? Carbon::parse($latestVerification->verified_at)->isoFormat('D MMM, HH:mm') : null;
        }

        // 6. 7-day progress calculation
        $this->loadWeeklyProgress($user->id);

        // 7. Top 3 Leaderboard
        $this->loadLeaderboard($class, $semester);
    }

    public function selectGroup(string $group): void
    {
        $this->activeGroup = $group;
    }

    public function toggleCheck(int $index): void
    {
        if (! isset($this->activities[$index])) {
            return;
        }

        $this->activities[$index]['checked'] = ! $this->activities[$index]['checked'];
        $this->activities[$index]['value'] = $this->activities[$index]['checked'] ? '1' : '0';
        $this->persistActivity($index);
    }

    public function updateTextValue(int $index, string $value): void
    {
        if (! isset($this->activities[$index])) {
            return;
        }

        $this->activities[$index]['value'] = $value;
        $this->activities[$index]['checked'] = trim($value) !== '';
        $this->persistActivity($index);
    }

    public function submitMutabaah(): void
    {
        foreach (array_keys($this->activities) as $index) {
            $this->persistActivity($index);
        }
        $this->submitted = true;
    }

    private function persistActivity(int $index): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $activity = $this->activities[$index];
        $today = Carbon::today()->toDateString();
        $school = $user->school;
        $academicYear = $school?->activeAcademicYear;
        $semester = $academicYear?->activeSemester;
        $class = $user->currentClass($semester);

        $log = StudentActivityLog::updateOrCreate(
            [
                'student_id' => $user->id,
                'activity_item_id' => $activity['item_id'],
                'logged_on' => $today,
            ],
            [
                'school_id' => $user->school_id,
                'class_id' => $class?->id,
                'semester_id' => $semester?->id,
                'value' => $activity['value'],
                'status' => 'submitted',
            ]
        );

        $this->activities[$index]['id'] = $log->id;
    }

    private function calculateStreak(int $userId): void
    {
        $streak = 0;
        $date = Carbon::today();

        for ($i = 0; $i < 30; $i++) {
            $checkDate = $date->copy()->subDays($i)->toDateString();
            $hasLog = StudentActivityLog::where('student_id', $userId)
                ->whereDate('logged_on', $checkDate)
                ->where(function ($q) {
                    $q->where('value', '1')->orWhere('value', '!=', '');
                })
                ->exists();

            if ($hasLog) {
                $streak++;
            } elseif ($i > 0) {
                break;
            }
        }

        $this->streakDays = max(1, $streak);
    }

    private function loadWeeklyProgress(int $userId): void
    {
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $count = StudentActivityLog::where('student_id', $userId)
                ->whereDate('logged_on', $d->toDateString())
                ->where(function ($q) {
                    $q->where('value', '1')->orWhere('value', '!=', '');
                })
                ->count();

            $total = count($this->activities) ?: 8;
            $percent = min(100, (int) round(($count / $total) * 100));

            $dayLabel = match ($d->dayOfWeekIso) {
                1 => 'Sen',
                2 => 'Sel',
                3 => 'Rab',
                4 => 'Kam',
                5 => 'Jum',
                6 => 'Sab',
                default => 'Min',
            };

            $days[] = [
                'day' => $i === 0 ? 'Hari ini' : $dayLabel,
                'percent' => $percent,
                'points' => $count * 10,
                'is_today' => $i === 0,
            ];
        }
        $this->recentWeeklyProgress = $days;
    }

    private function loadLeaderboard(?object $class, ?object $semester): void
    {
        if (! $class) {
            return;
        }

        $studentIds = ClassStudent::where('class_id', $class->id)
            ->when($semester, fn ($q) => $q->where('semester_id', $semester->id))
            ->whereNull('left_at')
            ->pluck('student_id');

        $scores = StudentPoint::whereIn('student_id', $studentIds)
            ->when($semester, fn ($q) => $q->where('semester_id', $semester->id))
            ->selectRaw('student_id, SUM(points) as total_points')
            ->groupBy('student_id')
            ->with('student')
            ->orderByDesc('total_points')
            ->take(5)
            ->get();

        $leaderboard = [];
        $rank = 1;
        foreach ($scores as $item) {
            $student = $item->student;
            if (! $student) {
                continue;
            }
            $leaderboard[] = [
                'rank' => $rank,
                'id' => $student->id,
                'name' => $student->name,
                'initials' => strtoupper(substr($student->name, 0, 2)),
                'points' => (int) $item->total_points,
                'is_me' => $student->id === Auth::id(),
            ];
            $rank++;
        }

        $this->topLeaderboard = $leaderboard;
    }

    public function getCompletedCountProperty(): int
    {
        return count(array_filter($this->activities, fn (array $a): bool => (bool) $a['checked']));
    }

    public function getProgressProperty(): int
    {
        $total = count($this->activities);
        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->completedCount / $total) * 100);
    }

    public function render()
    {
        $groups = ActivityGroup::where('is_active', true)->orderBy('sort_order')->get();

        return view('livewire.student.dashboard', [
            'groups' => $groups,
        ]);
    }
}
