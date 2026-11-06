<?php

namespace Tests\Feature;

use App\Livewire\Teacher\AcademicYears;
use App\Livewire\Teacher\Classes;
use App\Livewire\Teacher\Parents;
use App\Livewire\Teacher\SchoolReports;
use App\Livewire\Teacher\Students;
use App\Livewire\Teacher\Teachers;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeacherManagementModulesTest extends TestCase
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

    public function test_teacher_can_access_all_school_management_routes(): void
    {
        $school = School::create(['code' => 'SCH-02', 'name' => 'SDIT Guru Test', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $year = AcademicYear::create(['school_id' => $school->id, 'name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);
        Semester::create(['academic_year_id' => $year->id, 'name' => 'Ganjil', 'starts_on' => '2026-07-01', 'ends_on' => '2026-12-31', 'is_active' => true]);

        $teacher = User::factory()->create(['school_id' => $school->id, 'name' => 'Ust. Guru']);
        $teacher->syncRoles(['teacher']);

        $this->actingAs($teacher);

        // Teacher dashboard and modules
        $this->get(route('teacher.dashboard'))->assertStatus(200)->assertSee('Modul Manajemen Sekolah');
        $this->get(route('teacher.classes'))->assertStatus(200);
        $this->get(route('teacher.academic-years'))->assertStatus(200);
        $this->get(route('teacher.teachers'))->assertStatus(200);
        $this->get(route('teacher.students'))->assertStatus(200);
        $this->get(route('teacher.parents'))->assertStatus(200);
        $this->get(route('teacher.school-reports'))->assertStatus(200);
        $this->get(route('teacher.verification'))->assertStatus(200);
        $this->get(route('teacher.reports'))->assertStatus(200);
    }

    public function test_teacher_can_perform_school_admin_actions(): void
    {
        $school = School::create(['code' => 'SCH-03', 'name' => 'SDIT Maju', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $year = AcademicYear::create(['school_id' => $school->id, 'name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);
        Semester::create(['academic_year_id' => $year->id, 'name' => 'Ganjil', 'starts_on' => '2026-07-01', 'ends_on' => '2026-12-31', 'is_active' => true]);

        $teacher = User::factory()->create(['school_id' => $school->id, 'name' => 'Guru Pengelola']);
        $teacher->syncRoles(['teacher']);

        $this->actingAs($teacher);

        // 1. Manage Class
        Livewire::test(Classes::class)
            ->set('className', 'Kelas 6A')
            ->set('gradeLevel', 6)
            ->set('academicYearId', $year->id)
            ->set('isActive', true)
            ->call('save');

        $this->assertDatabaseHas('classes', [
            'school_id' => $school->id,
            'name' => 'Kelas 6A',
            'grade_level' => 6,
        ]);

        $class = SchoolClass::where('name', 'Kelas 6A')->first();

        // 2. Manage Student
        Livewire::test(Students::class)
            ->set('name', 'Budi Santoso')
            ->set('email', 'budi@test.com')
            ->set('studentNumber', '202606001')
            ->set('initialClassId', $class->id)
            ->call('saveStudent');

        $this->assertDatabaseHas('users', ['email' => 'budi@test.com']);
        $this->assertDatabaseHas('class_students', ['class_id' => $class->id]);

        // 3. Manage Parent
        $student = User::where('email', 'budi@test.com')->first();
        Livewire::test(Parents::class)
            ->set('name', 'Wali Budi')
            ->set('email', 'wali.budi@test.com')
            ->call('saveParent');

        $parent = User::where('email', 'wali.budi@test.com')->first();
        Livewire::test(Parents::class)
            ->set('selectedParentId', $parent->id)
            ->set('selectedStudentId', $student->id)
            ->call('linkStudent');

        $this->assertDatabaseHas('parent_student', [
            'parent_id' => $parent->id,
            'student_id' => $student->id,
        ]);

        // 4. Manage Teacher
        Livewire::test(Teachers::class)
            ->set('name', 'Ustadzah Fatimah')
            ->set('email', 'fatimah@test.com')
            ->call('saveTeacher');

        $this->assertDatabaseHas('users', ['email' => 'fatimah@test.com']);
    }

    public function test_student_cannot_access_teacher_management_routes(): void
    {
        $school = School::create(['code' => 'SCH-04', 'name' => 'SDIT Siswa', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $student = User::factory()->create(['school_id' => $school->id]);
        $student->syncRoles(['student']);

        $this->actingAs($student);

        $this->get(route('teacher.dashboard'))->assertStatus(403);
        $this->get(route('teacher.classes'))->assertStatus(403);
        $this->get(route('teacher.academic-years'))->assertStatus(403);
        $this->get(route('teacher.teachers'))->assertStatus(403);
        $this->get(route('teacher.students'))->assertStatus(403);
        $this->get(route('teacher.parents'))->assertStatus(403);
        $this->get(route('teacher.school-reports'))->assertStatus(403);
    }
}

