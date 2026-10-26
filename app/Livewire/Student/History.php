<?php

namespace App\Livewire\Student;

use App\Models\ActivityGroup;
use App\Models\StudentActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Riwayat Kegiatan — AMALIN')]
class History extends Component
{
    public string $selectedMonth = '';

    public string $selectedGroup = 'all';

    public function mount(): void
    {
        $this->selectedMonth = now()->format('Y-m');
    }

    public function render()
    {
        $user = Auth::user();
        $startOfMonth = Carbon::parse($this->selectedMonth.'-01')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $query = StudentActivityLog::where('student_id', $user->id)
            ->whereBetween('logged_on', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->with(['activityItem.activityGroup', 'verification.teacher'])
            ->orderByDesc('logged_on');

        if ($this->selectedGroup !== 'all') {
            $query->whereHas('activityItem', function ($q) {
                $q->where('activity_group_id', $this->selectedGroup);
            });
        }

        $logs = $query->get()->groupBy(fn ($log) => $log->logged_on->toDateString());

        $groups = ActivityGroup::where('is_active', true)->orderBy('sort_order')->get();

        $totalLoggedDays = StudentActivityLog::where('student_id', $user->id)
            ->whereBetween('logged_on', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->distinct('logged_on')
            ->count('logged_on');

        $totalApprovedPoints = $user->points()
            ->whereBetween('credited_at', [$startOfMonth->startOfDay(), $endOfMonth->endOfDay()])
            ->sum('points');

        return view('livewire.student.history', [
            'groupedLogs' => $logs,
            'groups' => $groups,
            'totalLoggedDays' => $totalLoggedDays,
            'totalApprovedPoints' => $totalApprovedPoints,
        ]);
    }
}
