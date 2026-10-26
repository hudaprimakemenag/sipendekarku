<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['school_id', 'student_id', 'semester_id', 'activity_verification_id', 'points', 'description', 'credited_at'])]
class StudentPoint extends Model
{
    protected function casts(): array
    {
        return ['credited_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function verification(): BelongsTo
    {
        return $this->belongsTo(ActivityVerification::class, 'activity_verification_id');
    }
}
