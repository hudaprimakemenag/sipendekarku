<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['school_id', 'student_id', 'class_id', 'semester_id', 'activity_item_id', 'logged_on', 'value', 'status'])]
class StudentActivityLog extends Model
{
    protected function casts(): array
    {
        return ['logged_on' => 'date'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function activityItem(): BelongsTo
    {
        return $this->belongsTo(ActivityItem::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function verification(): HasOne
    {
        return $this->hasOne(ActivityVerification::class);
    }
}
