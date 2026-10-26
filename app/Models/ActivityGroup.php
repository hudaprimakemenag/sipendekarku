<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'sort_order', 'is_active'])]
class ActivityGroup extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(ActivityItem::class);
    }

    public function activityItems(): HasMany
    {
        return $this->hasMany(ActivityItem::class);
    }

    public function activeItems(): HasMany
    {
        return $this->hasMany(ActivityItem::class)->where('is_active', true)->orderBy('sort_order');
    }
}
