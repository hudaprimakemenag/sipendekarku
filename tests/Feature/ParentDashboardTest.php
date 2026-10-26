<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ParentDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
    }

    public function test_parent_can_view_child_monitoring_dashboard(): void
    {
        $school = School::create(['code' => 'SCH-01', 'name' => 'SDIT Test', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $year = AcademicYear::create(['school_id' => $school->id, 'name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);
        $semester = Semester::create(['academic_year_id' => $year->id, 'name' => 'Ganjil', 'starts_on' => '2026-07-01', 'ends_on' => '2026-12-31', 'is_active' => true]);
        $class = SchoolClass::create(['school_id' => $school->id, 'academic_year_id' => $year->id, 'name' => '5A', 'grade_level' => 5, 'is_active' => true]);

        $parent = User::factory()->create(['school_id' => $school->id, 'name' => 'Bpk. Rahman']);
        $parent->syncRoles(['parent']);

        $student = User::factory()->create(['school_id' => $school->id, 'name' => 'Naila Azzahra']);
        $student->syncRoles(['student']);

        DB::table('parent_student')->insert([
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ClassStudent::create(['class_id' => $class->id, 'student_id' => $student->id, 'semester_id' => $semester->id, 'assigned_at' => now()]);

        $response = $this->actingAs($parent)->get(route('parent.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Naila Azzahra');
        $response->assertSee('Pemantauan Mutaba', false);
    }
}

