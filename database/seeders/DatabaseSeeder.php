<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ActivityGroup;
use App\Models\ActivityItem;
use App\Models\ActivityVerification;
use App\Models\ClassStudent;
use App\Models\ClassTeacher;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\StudentActivityLog;
use App\Models\StudentPoint;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /* ── 1. Roles ─────────────────────────────────────────────── */
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $schoolAdminRole = Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $parentRole = Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);

        /* ── 2. School ────────────────────────────────────────────── */
        $school = School::firstOrCreate(
            ['code' => 'SDIT-001'],
            [
                'name' => 'SDIT Al-Hikmah',
                'address' => 'Jl. Pendidikan Karakter No. 1, Jakarta Selatan',
                'timezone' => 'Asia/Jakarta',
                'is_active' => true,
            ]
        );

        /* ── 3. Academic Year & Semester ──────────────────────────── */
        $academicYear = AcademicYear::firstOrCreate(
            ['school_id' => $school->id, 'name' => '2026/2027'],
            [
                'starts_on' => '2026-07-14',
                'ends_on' => '2027-06-20',
                'is_active' => true,
            ]
        );

        $semester = Semester::firstOrCreate(
            ['academic_year_id' => $academicYear->id, 'name' => 'Ganjil'],
            [
                'starts_on' => '2026-07-14',
                'ends_on' => '2026-12-20',
                'is_active' => true,
            ]
        );

        /* ── 4. Users ─────────────────────────────────────────────── */
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@tarbiyah.id'],
            ['name' => 'Superadmin AMALIN', 'password' => Hash::make('password')]
        );
        $superadmin->syncRoles([$superadminRole]);

        $adminSekolah = User::firstOrCreate(
            ['email' => 'admin@sekolah.id'],
            ['name' => 'Admin SDIT Al-Hikmah', 'school_id' => $school->id, 'password' => Hash::make('password')]
        );
        $adminSekolah->syncRoles([$schoolAdminRole]);

        $guru = User::firstOrCreate(
            ['email' => 'guru@sekolah.id'],
            ['name' => 'Ustadz Ahmad Fauzi', 'school_id' => $school->id, 'password' => Hash::make('password')]
        );
        $guru->syncRoles([$teacherRole]);

        $siswa = User::firstOrCreate(
            ['email' => 'siswa@sekolah.id'],
            ['name' => 'Naila Azzahra', 'school_id' => $school->id, 'student_number' => '202605001', 'password' => Hash::make('password')]
        );
        $siswa->syncRoles([$studentRole]);

        // Siswa tambahan untuk demo kelas
        $siswa2 = User::firstOrCreate(
            ['email' => 'rafa@sekolah.id'],
            ['name' => 'Rafa Alfarizi', 'school_id' => $school->id, 'student_number' => '202605002', 'password' => Hash::make('password')]
        );
        $siswa2->syncRoles([$studentRole]);

        $siswa3 = User::firstOrCreate(
            ['email' => 'khadijah@sekolah.id'],
            ['name' => 'Khadijah Aulia', 'school_id' => $school->id, 'student_number' => '202605003', 'password' => Hash::make('password')]
        );
        $siswa3->syncRoles([$studentRole]);

        $wali = User::firstOrCreate(
            ['email' => 'wali@sekolah.id'],
            ['name' => 'Bpk. Rahman (Wali Naila)', 'school_id' => $school->id, 'password' => Hash::make('password')]
        );
        $wali->syncRoles([$parentRole]);

        /* ── 5. Parent-Student Link ───────────────────────────────── */
        DB::table('parent_student')->insertOrIgnore([
            ['parent_id' => $wali->id, 'student_id' => $siswa->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        /* ── 6. Classes ───────────────────────────────────────────── */
        $class5A = SchoolClass::firstOrCreate(
            ['school_id' => $school->id, 'academic_year_id' => $academicYear->id, 'name' => 'Kelas 5A'],
            ['grade_level' => 5, 'is_active' => true]
        );
        $class5B = SchoolClass::firstOrCreate(
            ['school_id' => $school->id, 'academic_year_id' => $academicYear->id, 'name' => 'Kelas 5B'],
            ['grade_level' => 5, 'is_active' => true]
        );

        /* ── 7. Class Assignments ─────────────────────────────────── */
        ClassStudent::firstOrCreate(
            ['class_id' => $class5A->id, 'student_id' => $siswa->id, 'semester_id' => $semester->id],
            ['assigned_at' => now()->subDays(60)]
        );
        ClassStudent::firstOrCreate(
            ['class_id' => $class5A->id, 'student_id' => $siswa2->id, 'semester_id' => $semester->id],
            ['assigned_at' => now()->subDays(60)]
        );
        ClassStudent::firstOrCreate(
            ['class_id' => $class5A->id, 'student_id' => $siswa3->id, 'semester_id' => $semester->id],
            ['assigned_at' => now()->subDays(60)]
        );

        ClassTeacher::firstOrCreate(
            ['class_id' => $class5A->id, 'teacher_id' => $guru->id, 'semester_id' => $semester->id],
            ['assigned_at' => now()->subDays(60)]
        );
        ClassTeacher::firstOrCreate(
            ['class_id' => $class5B->id, 'teacher_id' => $guru->id, 'semester_id' => $semester->id],
            ['assigned_at' => now()->subDays(60)]
        );

        /* ── 8. Activity Groups & Items ───────────────────────────── */
        $ibadah = ActivityGroup::firstOrCreate(['name' => 'Ibadah'], ['sort_order' => 1, 'is_active' => true]);
        $akhlak = ActivityGroup::firstOrCreate(['name' => 'Akhlak'], ['sort_order' => 2, 'is_active' => true]);
        $sosial = ActivityGroup::firstOrCreate(['name' => 'Sosial'], ['sort_order' => 3, 'is_active' => true]);

        $items = [
            // Ibadah
            ['group' => $ibadah, 'code' => 'IBD-001', 'name' => 'Shalat Subuh', 'input_type' => 'checklist', 'point_weight' => 10, 'sort_order' => 1],
            ['group' => $ibadah, 'code' => 'IBD-002', 'name' => 'Shalat Dzuhur', 'input_type' => 'checklist', 'point_weight' => 10, 'sort_order' => 2],
            ['group' => $ibadah, 'code' => 'IBD-003', 'name' => 'Shalat Ashar', 'input_type' => 'checklist', 'point_weight' => 10, 'sort_order' => 3],
            ['group' => $ibadah, 'code' => 'IBD-004', 'name' => 'Shalat Maghrib', 'input_type' => 'checklist', 'point_weight' => 10, 'sort_order' => 4],
            ['group' => $ibadah, 'code' => 'IBD-005', 'name' => 'Shalat Isya', 'input_type' => 'checklist', 'point_weight' => 10, 'sort_order' => 5],
            ['group' => $ibadah, 'code' => 'IBD-006', 'name' => 'Tilawah Al-Qur\'an', 'input_type' => 'text', 'point_weight' => 5, 'sort_order' => 6],
            ['group' => $ibadah, 'code' => 'IBD-007', 'name' => 'Dzikir Pagi', 'input_type' => 'checklist', 'point_weight' => 5, 'sort_order' => 7],
            // Akhlak
            ['group' => $akhlak, 'code' => 'AKH-001', 'name' => 'Berkata Santun', 'input_type' => 'checklist', 'point_weight' => 10, 'sort_order' => 1],
            ['group' => $akhlak, 'code' => 'AKH-002', 'name' => 'Merapikan Kamar', 'input_type' => 'checklist', 'point_weight' => 5, 'sort_order' => 2],
            // Sosial
            ['group' => $sosial, 'code' => 'SOS-001', 'name' => 'Membantu Orang Tua', 'input_type' => 'checklist', 'point_weight' => 10, 'sort_order' => 1],
            ['group' => $sosial, 'code' => 'SOS-002', 'name' => 'Infaq', 'input_type' => 'text', 'point_weight' => 5, 'sort_order' => 2],
        ];

        $createdItems = [];
        foreach ($items as $itemData) {
            $group = $itemData['group'];
            $item = ActivityItem::firstOrCreate(
                ['code' => $itemData['code']],
                [
                    'activity_group_id' => $group->id,
                    'name' => $itemData['name'],
                    'input_type' => $itemData['input_type'],
                    'point_weight' => $itemData['point_weight'],
                    'sort_order' => $itemData['sort_order'],
                    'is_active' => true,
                ]
            );
            $createdItems[] = $item;
        }

        /* ── 9. Demo Activity Logs (7 days for Naila) ─────────────── */
        $this->seedStudentLogs($siswa, $siswa2, $siswa3, $school, $class5A, $semester, $createdItems, $guru);
    }

    /**
     * Seed demo activity logs and verifications.
     *
     * @param  array<int, ActivityItem>  $items
     */
    private function seedStudentLogs(
        User $siswa,
        User $siswa2,
        User $siswa3,
        School $school,
        SchoolClass $class,
        Semester $semester,
        array $items,
        User $guru
    ): void {
        // Seed 6 past days for Naila (fully locked + some verified)
        for ($daysAgo = 6; $daysAgo >= 1; $daysAgo--) {
            $date = Carbon::today()->subDays($daysAgo);
            // Skip weekends for realism
            if ($date->isWeekend()) {
                continue;
            }

            foreach ($items as $item) {
                $value = $item->input_type === 'checklist' ? '1' : ($item->code === 'IBD-006' ? '2' : 'Rp 5.000');

                $log = StudentActivityLog::firstOrCreate(
                    ['student_id' => $siswa->id, 'activity_item_id' => $item->id, 'logged_on' => $date->toDateString()],
                    [
                        'school_id' => $school->id,
                        'class_id' => $class->id,
                        'semester_id' => $semester->id,
                        'value' => $value,
                        'status' => 'locked',
                    ]
                );

                // Verify most items (random skip ~15%)
                if (rand(1, 100) > 15 && ! $log->verification) {
                    $verification = ActivityVerification::create([
                        'student_activity_log_id' => $log->id,
                        'teacher_id' => $guru->id,
                        'status' => 'approved',
                        'notes' => null,
                        'credited_points' => $item->point_weight,
                        'verified_at' => $date->copy()->addHours(rand(8, 14)),
                    ]);

                    StudentPoint::firstOrCreate(
                        ['activity_verification_id' => $verification->id],
                        [
                            'school_id' => $school->id,
                            'student_id' => $siswa->id,
                            'semester_id' => $semester->id,
                            'points' => $item->point_weight,
                            'description' => $item->name,
                            'credited_at' => $verification->verified_at,
                        ]
                    );
                }
            }
        }

        // Seed today's logs for Naila (submitted, not locked)
        foreach ($items as $idx => $item) {
            $value = match (true) {
                $item->input_type === 'checklist' && $idx < 6 => '1',   // first 6 checked
                $item->code === 'IBD-006' => '2',                        // tilawah 2 halaman
                $item->code === 'SOS-002' => '',                         // infaq belum diisi
                default => '',
            };

            StudentActivityLog::firstOrCreate(
                ['student_id' => $siswa->id, 'activity_item_id' => $item->id, 'logged_on' => today()->toDateString()],
                [
                    'school_id' => $school->id,
                    'class_id' => $class->id,
                    'semester_id' => $semester->id,
                    'value' => $value,
                    'status' => 'submitted',
                ]
            );
        }

        // Seed yesterday's logs for siswa2 & siswa3 (for guru verification demo)
        $yesterday = Carbon::yesterday();
        foreach ([$siswa2, $siswa3] as $s) {
            foreach ($items as $item) {
                $log = StudentActivityLog::firstOrCreate(
                    ['student_id' => $s->id, 'activity_item_id' => $item->id, 'logged_on' => $yesterday->toDateString()],
                    [
                        'school_id' => $school->id,
                        'class_id' => $class->id,
                        'semester_id' => $semester->id,
                        'value' => $item->input_type === 'checklist' ? '1' : '2',
                        'status' => 'locked',
                    ]
                );
                // Leave unverified so guru can verify in demo
                unset($log);
            }
        }

        // Also give siswa2 & siswa3 some verified points for leaderboard
        foreach ([$siswa2, $siswa3] as $idx => $s) {
            $basePoints = $idx === 0 ? 1420 : 1325;
            if (! StudentPoint::where('student_id', $s->id)->exists()) {
                StudentPoint::create([
                    'school_id' => $school->id,
                    'student_id' => $s->id,
                    'semester_id' => $semester->id,
                    'activity_verification_id' => null,
                    'points' => $basePoints,
                    'description' => 'Poin demo awal semester',
                    'credited_at' => now()->subDays(30),
                ]);
            }
        }
    }
}
