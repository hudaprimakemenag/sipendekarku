<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'school_id', 'phone', 'student_number', 'is_active', 'graduated_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'graduated_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /** Students linked to this parent */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_id', 'student_id')
            ->withTimestamps();
    }

    /** Parents linked to this student */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id')
            ->withTimestamps();
    }

    /** Classes this student belongs to */
    public function classAssignments(): HasMany
    {
        return $this->hasMany(ClassStudent::class, 'student_id');
    }

    /** Classes this teacher teaches */
    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(ClassTeacher::class, 'teacher_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(StudentActivityLog::class, 'student_id');
    }

    public function points(): HasMany
    {
        return $this->hasMany(StudentPoint::class, 'student_id');
    }

    /** Convenience: first role name for redirect logic */
    public function primaryRole(): string
    {
        return $this->getRoleNames()->first() ?? 'student';
    }

    /** Dashboard route name based on role */
    public function dashboardRoute(): string
    {
        return match ($this->primaryRole()) {
            'superadmin' => 'superadmin.dashboard',
            'school_admin' => 'admin.dashboard',
            'teacher' => 'teacher.dashboard',
            'parent' => 'parent.dashboard',
            default => 'student.dashboard',
        };
    }

    /** Current assigned class for student */
    public function currentClass(?Semester $semester = null): ?SchoolClass
    {
        $query = $this->classAssignments()->with('schoolClass')->whereNull('left_at');
        if ($semester) {
            $query->where('semester_id', $semester->id);
        }
        $assignment = $query->latest('assigned_at')->first();

        return $assignment?->schoolClass;
    }

    /** Current teaching classes for teacher */
    public function currentTeachingClasses(?Semester $semester = null)
    {
        $query = ClassTeacher::where('teacher_id', $this->id)->whereNull('unassigned_at')->with('schoolClass');
        if ($semester) {
            $query->where('semester_id', $semester->id);
        }

        return $query->get()->pluck('schoolClass')->filter();
    }

    /** Total verified points for student */
    public function totalVerifiedPoints(?Semester $semester = null): int
    {
        $query = $this->points();
        if ($semester) {
            $query->where('semester_id', $semester->id);
        }

        return (int) $query->sum('points');
    }

    /** Class rank for student in current semester */
    public function classRank(?Semester $semester = null): int
    {
        $class = $this->currentClass($semester);
        if (! $class) {
            return 1;
        }

        $semesterId = $semester?->id ?? $this->school?->activeAcademicYear?->activeSemester?->id;

        $studentIds = ClassStudent::where('class_id', $class->id)
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->whereNull('left_at')
            ->pluck('student_id');

        $scores = StudentPoint::whereIn('student_id', $studentIds)
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->selectRaw('student_id, SUM(points) as total_points')
            ->groupBy('student_id')
            ->orderByDesc('total_points')
            ->pluck('total_points', 'student_id')
            ->all();

        // Ensure all students in class are included in ranking
        $allStudentScores = [];
        foreach ($studentIds as $sid) {
            $allStudentScores[$sid] = $scores[$sid] ?? 0;
        }
        arsort($allStudentScores);

        $rank = 1;
        foreach (array_keys($allStudentScores) as $sid) {
            if ($sid === $this->id) {
                return $rank;
            }
            $rank++;
        }

        return $rank;
    }
}
