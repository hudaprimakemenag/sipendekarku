<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
    }

    public function test_can_export_csv_and_render_printable_views(): void
    {
        $school = School::create(['code' => 'SCH-01', 'name' => 'SDIT Test', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $year = AcademicYear::create(['school_id' => $school->id, 'name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true]);
        $semester = Semester::create(['academic_year_id' => $year->id, 'name' => 'Ganjil', 'starts_on' => '2026-07-01', 'ends_on' => '2026-12-31', 'is_active' => true]);
        $class = SchoolClass::create(['school_id' => $school->id, 'academic_year_id' => $year->id, 'name' => '5A', 'grade_level' => 5, 'is_active' => true]);

        $student = User::factory()->create(['school_id' => $school->id, 'name' => 'Naila Azzahra']);
        $student->syncRoles(['student']);

        ClassStudent::create(['class_id' => $class->id, 'student_id' => $student->id, 'semester_id' => $semester->id, 'assigned_at' => now()]);

        // Student CSV Export
        $response = $this->actingAs($student)->get(route('reports.export.csv', ['type' => 'student', 'student_id' => $student->id]));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // Printable student view
        $response = $this->actingAs($student)->get(route('reports.printable', ['type' => 'student', 'student_id' => $student->id]));
        $response->assertStatus(200);
        $response->assertSee('Raport Mutaba\'ah Siswa');
        $response->assertSee('Naila Azzahra');

        // Printable class view
        $teacher = User::factory()->create(['school_id' => $school->id, 'name' => 'Guru Pengampu']);
        $teacher->syncRoles(['teacher']);

        $response = $this->actingAs($teacher)->get(route('reports.printable', ['type' => 'class', 'class_id' => $class->id]));
        $response->assertStatus(200);
        $response->assertSee('Laporan Rekapitulasi Kelas 5A');

        // Printable global view
        $superadmin = User::factory()->create(['name' => 'Superadmin']);
        $superadmin->syncRoles(['superadmin']);

        $response = $this->actingAs($superadmin)->get(route('reports.printable', ['type' => 'global']));
        $response->assertStatus(200);
        $response->assertSee('Laporan Komparasi Multi-Sekolah');
    }
}
