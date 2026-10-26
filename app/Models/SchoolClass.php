<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['school_id', 'academic_year_id', 'name', 'grade_level', 'is_active'])]
class SchoolClass extends Model
{
    protected $table = 'classes';

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classStudents(): HasMany
    {
        return $this->hasMany(ClassStudent::class, 'class_id');
    }

    public function classTeachers(): HasMany
    {
        return $this->hasMany(ClassTeacher::class, 'class_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_students', 'class_id', 'student_id')
            ->withPivot(['semester_id', 'assigned_at', 'left_at'])
            ->withTimestamps();
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_teachers', 'class_id', 'teacher_id')
            ->withPivot(['semester_id', 'assigned_at', 'unassigned_at'])
            ->withTimestamps();
    }
}
