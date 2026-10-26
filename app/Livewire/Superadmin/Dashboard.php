<?php

namespace App\Livewire\Superadmin;

use App\Models\ActivityGroup;
use App\Models\ActivityItem;
use App\Models\School;
use App\Models\StudentActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Superadmin Dashboard — SIPENDEKARKU')]
class Dashboard extends Component
{
    public function render()
    {
        $totalSchools = School::count();
        $activeSchools = School::where('is_active', true)->count();

        $totalUsers = User::count();
        $totalStudents = User::role('student')->count();
        $totalTeachers = User::role('teacher')->count();
        $totalSchoolAdmins = User::role('school_admin')->count();

        $todayLogs = StudentActivityLog::whereDate('logged_on', Carbon::today())
            ->where(function ($q) {
                $q->where('value', '1')->orWhere('value', '!=', '');
            })
            ->count();

        $totalGroups = ActivityGroup::count();
        $totalItems = ActivityItem::count();

        $schools = School::withCount(['users', 'classes'])->get();

        return view('livewire.superadmin.dashboard', [
            'totalSchools' => $totalSchools,
            'activeSchools' => $activeSchools,
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalSchoolAdmins' => $totalSchoolAdmins,
            'todayLogs' => $todayLogs,
            'totalGroups' => $totalGroups,
            'totalItems' => $totalItems,
            'schools' => $schools,
        ]);
    }
}
