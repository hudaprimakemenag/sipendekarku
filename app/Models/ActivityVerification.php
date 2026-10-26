<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['student_activity_log_id', 'teacher_id', 'status', 'notes', 'credited_points', 'verified_at'])]
class ActivityVerification extends Model
{
    protected function casts(): array
    {
        return ['verified_at' => 'datetime'];
    }

    public function log(): BelongsTo
    {
        return $this->belongsTo(StudentActivityLog::class, 'student_activity_log_id');
    }

    public function studentActivityLog(): BelongsTo
    {
        return $this->belongsTo(StudentActivityLog::class, 'student_activity_log_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function studentPoint(): HasOne
    {
        return $this->hasOne(StudentPoint::class, 'activity_verification_id');
    }
}
