<?php

namespace Tests\Feature;

use App\Livewire\SchoolAdmin\Classes;
use App\Livewire\SchoolAdmin\Parents;
use App\Livewire\SchoolAdmin\Students;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SchoolAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
    }

    public function test_school_admin_can_manage_classes_and_users(): void
    {
        $school = School::create(['code' => 'SCH-01', 'name' => 'SDIT Test', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $year = AcademicYear::create(['school_id' => $school->id, 'name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);
        $semester = Semester::create(['academic_year_id' => $year->id, 'name' => 'Ganjil', 'starts_on' => '2026-07-01', 'ends_on' => '2026-12-31', 'is_active' => true]);

        $admin = User::factory()->create(['school_id' => $school->id, 'name' => 'Admin Sekolah']);
        $admin->syncRoles(['school_admin']);

        $this->actingAs($admin);

        // Test create class
        Livewire::test(Classes::class)
            ->set('className', 'Kelas 5B')
            ->set('gradeLevel', 5)
            ->set('academicYearId', $year->id)
            ->set('isActive', true)
            ->call('save');

        $this->assertDatabaseHas('classes', [
            'school_id' => $school->id,
            'name' => 'Kelas 5B',
            'grade_level' => 5,
        ]);

        $class = SchoolClass::where('name', 'Kelas 5B')->first();

        // Test create student and auto-assign
        Livewire::test(Students::class)
            ->set('name', 'Ahmad Dani')
            ->set('email', 'dani@test.com')
            ->set('studentNumber', '202605999')
            ->set('initialClassId', $class->id)
            ->call('saveStudent');

        $this->assertDatabaseHas('users', ['email' => 'dani@test.com']);
        $this->assertDatabaseHas('class_students', ['class_id' => $class->id]);

        // Test create parent and link to student
        $student = User::where('email', 'dani@test.com')->first();
        Livewire::test(Parents::class)
            ->set('name', 'Bpk. Dani')
            ->set('email', 'wali.dani@test.com')
            ->call('saveParent');

        $parent = User::where('email', 'wali.dani@test.com')->first();

        Livewire::test(Parents::class)
            ->set('selectedParentId', $parent->id)
            ->set('selectedStudentId', $student->id)
            ->call('linkStudent');

        $this->assertDatabaseHas('parent_student', [
            'parent_id' => $parent->id,
            'student_id' => $student->id,
        ]);
    }
}
