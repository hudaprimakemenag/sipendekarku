<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['activity_group_id', 'code', 'name', 'input_type', 'point_weight', 'sort_order', 'is_active'])]
class ActivityItem extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ActivityGroup::class, 'activity_group_id');
    }

    public function activityGroup(): BelongsTo
    {
        return $this->belongsTo(ActivityGroup::class, 'activity_group_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(StudentActivityLog::class);
    }
}
