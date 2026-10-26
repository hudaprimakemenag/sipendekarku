<?php

namespace App\Livewire\Superadmin;

use App\Models\ActivityGroup;
use App\Models\ActivityItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Katalog Kegiatan Global — AMALIN')]
class Catalog extends Component
{
    public bool $showGroupModal = false;

    public bool $showItemModal = false;

    // Group Model
    public ?int $editingGroupId = null;

    public string $groupName = '';

    public int $groupSortOrder = 0;

    public bool $groupIsActive = true;

    // Item Model
    public ?int $editingItemId = null;

    public ?int $itemGroupId = null;

    public string $itemCode = '';

    public string $itemName = '';

    public string $itemInputType = 'checklist'; // checklist or text

    public int $itemPointWeight = 10;

    public int $itemSortOrder = 0;

    public bool $itemIsActive = true;

    public ?string $message = null;

    public function openCreateGroup(): void
    {
        $this->reset(['editingGroupId', 'groupName']);
        $this->groupSortOrder = ActivityGroup::count() + 1;
        $this->groupIsActive = true;
        $this->showGroupModal = true;
    }

    public function openEditGroup(int $id): void
    {
        $grp = ActivityGroup::findOrFail($id);
        $this->editingGroupId = $grp->id;
        $this->groupName = $grp->name;
        $this->groupSortOrder = $grp->sort_order;
        $this->groupIsActive = $grp->is_active;
        $this->showGroupModal = true;
    }

    public function saveGroup(): void
    {
        $this->validate([
            'groupName' => 'required|string|max:50|unique:activity_groups,name,'.$this->editingGroupId,
            'groupSortOrder' => 'required|integer|min:0',
        ]);

        ActivityGroup::updateOrCreate(
            ['id' => $this->editingGroupId],
            [
                'name' => $this->groupName,
                'sort_order' => $this->groupSortOrder,
                'is_active' => $this->groupIsActive,
            ]
        );

        $this->showGroupModal = false;
        $this->message = 'Kelompok kegiatan berhasil disimpan.';
    }

    public function openCreateItem(?int $groupId = null): void
    {
        $this->reset(['editingItemId', 'itemCode', 'itemName']);
        $this->itemGroupId = $groupId ?? ActivityGroup::first()?->id;
        $this->itemInputType = 'checklist';
        $this->itemPointWeight = 10;
        $this->itemSortOrder = ActivityItem::where('activity_group_id', $this->itemGroupId)->count() + 1;
        $this->itemIsActive = true;
        $this->showItemModal = true;
    }

    public function openEditItem(int $id): void
    {
        $item = ActivityItem::findOrFail($id);
        $this->editingItemId = $item->id;
        $this->itemGroupId = $item->activity_group_id;
        $this->itemCode = $item->code;
        $this->itemName = $item->name;
        $this->itemInputType = $item->input_type;
        $this->itemPointWeight = $item->point_weight;
        $this->itemSortOrder = $item->sort_order;
        $this->itemIsActive = $item->is_active;
        $this->showItemModal = true;
    }

    public function saveItem(): void
    {
        $this->validate([
            'itemGroupId' => 'required|exists:activity_groups,id',
            'itemCode' => 'required|string|max:30|unique:activity_items,code,'.$this->editingItemId,
            'itemName' => 'required|string|max:100',
            'itemInputType' => 'required|string|in:checklist,text',
            'itemPointWeight' => 'required|integer|min:0',
            'itemSortOrder' => 'required|integer|min:0',
        ]);

        ActivityItem::updateOrCreate(
            ['id' => $this->editingItemId],
            [
                'activity_group_id' => $this->itemGroupId,
                'code' => $this->itemCode,
                'name' => $this->itemName,
                'input_type' => $this->itemInputType,
                'point_weight' => $this->itemPointWeight,
                'sort_order' => $this->itemSortOrder,
                'is_active' => $this->itemIsActive,
            ]
        );

        $this->showItemModal = false;
        $this->message = 'Jenis kegiatan berhasil disimpan.';
    }

    public function toggleItemActive(int $id): void
    {
        $item = ActivityItem::findOrFail($id);
        $item->update(['is_active' => ! $item->is_active]);
        $status = $item->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->message = "Kegiatan {$item->name} telah {$status}.";
    }

    public function render()
    {
        $groups = ActivityGroup::with(['activityItems' => function ($q) {
            $q->orderBy('sort_order');
        }])->orderBy('sort_order')->get();

        return view('livewire.superadmin.catalog', [
            'groups' => $groups,
        ]);
    }
}
