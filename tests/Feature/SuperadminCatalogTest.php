<?php

namespace Tests\Feature;

use App\Livewire\Superadmin\Catalog;
use App\Livewire\Superadmin\Schools;
use App\Models\ActivityGroup;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuperadminCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);
    }

    public function test_superadmin_can_manage_schools_and_catalog(): void
    {
        $superadmin = User::factory()->create(['name' => 'Superadmin']);
        $superadmin->syncRoles(['superadmin']);

        $this->actingAs($superadmin);

        // Test create School
        Livewire::test(Schools::class)
            ->set('code', 'SDIT-002')
            ->set('name', 'SDIT Insan Mulia')
            ->set('address', 'Bandung')
            ->set('timezone', 'Asia/Jakarta')
            ->set('adminName', 'Admin Insan Mulia')
            ->set('adminEmail', 'admin@insanmulia.id')
            ->call('save');

        $this->assertDatabaseHas('schools', ['code' => 'SDIT-002', 'name' => 'SDIT Insan Mulia']);
        $this->assertDatabaseHas('users', ['email' => 'admin@insanmulia.id']);

        // Test create Activity Group & Item
        Livewire::test(Catalog::class)
            ->set('groupName', 'Kedisiplinan')
            ->set('groupSortOrder', 4)
            ->call('saveGroup');

        $group = ActivityGroup::where('name', 'Kedisiplinan')->first();
        $this->assertNotNull($group);

        Livewire::test(Catalog::class)
            ->set('itemGroupId', $group->id)
            ->set('itemCode', 'DIS-001')
            ->set('itemName', 'Datang Tepat Waktu')
            ->set('itemInputType', 'checklist')
            ->set('itemPointWeight', 15)
            ->set('itemSortOrder', 1)
            ->call('saveItem');

        $this->assertDatabaseHas('activity_items', [
            'code' => 'DIS-001',
            'name' => 'Datang Tepat Waktu',
            'point_weight' => 15,
        ]);
    }
}
