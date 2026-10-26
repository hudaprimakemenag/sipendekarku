<?php

namespace Tests\Feature;

use App\Livewire\Teacher\Verification;
use App\Models\AcademicYear;
use App\Models\ActivityGroup;
use App\Models\ActivityItem;
use App\Models\ClassStudent;
use App\Models\ClassTeacher;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\StudentActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeacherVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
    }

    public function test_teacher_can_verify_student_activity_and_credit_points(): void
    {
        $school = School::create(['code' => 'SCH-01', 'name' => 'SDIT Test', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $year = AcademicYear::create(['school_id' => $school->id, 'name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);
        $semester = Semester::create(['academic_year_id' => $year->id, 'name' => 'Ganjil', 'starts_on' => '2026-07-01', 'ends_on' => '2026-12-31', 'is_active' => true]);
        $class = SchoolClass::create(['school_id' => $school->id, 'academic_year_id' => $year->id, 'name' => '5A', 'grade_level' => 5, 'is_active' => true]);

        $teacher = User::factory()->create(['school_id' => $school->id, 'name' => 'Ustadz Ahmad']);
        $teacher->syncRoles(['teacher']);

        $student = User::factory()->create(['school_id' => $school->id, 'name' => 'Naila']);
        $student->syncRoles(['student']);

        ClassTeacher::create(['class_id' => $class->id, 'teacher_id' => $teacher->id, 'semester_id' => $semester->id, 'assigned_at' => now()]);
        ClassStudent::create(['class_id' => $class->id, 'student_id' => $student->id, 'semester_id' => $semester->id, 'assigned_at' => now()]);

        $group = ActivityGroup::create(['name' => 'Ibadah', 'sort_order' => 1, 'is_active' => true]);
        $item = ActivityItem::create([
            'activity_group_id' => $group->id,
            'code' => 'IBD-001',
            'name' => 'Shalat Subuh',
            'input_type' => 'checklist',
            'point_weight' => 10,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $log = StudentActivityLog::create([
            'school_id' => $school->id,
            'student_id' => $student->id,
            'class_id' => $class->id,
            'semester_id' => $semester->id,
            'activity_item_id' => $item->id,
            'logged_on' => Carbon::today()->toDateString(),
            'value' => '1',
            'status' => 'submitted',
        ]);

        $this->actingAs($teacher);

        Livewire::test(Verification::class, ['class_id' => $class->id])
            ->call('approve', $log->id);

        $this->assertDatabaseHas('activity_verifications', [
            'student_activity_log_id' => $log->id,
            'teacher_id' => $teacher->id,
            'status' => 'approved',
            'credited_points' => 10,
        ]);

        $this->assertDatabaseHas('student_points', [
            'student_id' => $student->id,
            'points' => 10,
        ]);
    }
}
