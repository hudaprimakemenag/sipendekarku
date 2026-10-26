<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LockExpiredActivityLogsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_command_locks_only_reports_before_the_cutoff_date(): void
    {
        $schoolId = DB::table('schools')->insertGetId([
            'code' => 'SDIT-AH',
            'name' => 'SDIT Al-Hikmah',
            'timezone' => 'Asia/Jakarta',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $student = User::factory()->create(['school_id' => $schoolId]);
        $activityGroupId = DB::table('activity_groups')->insertGetId([
            'name' => 'Ibadah',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $activityItemId = DB::table('activity_items')->insertGetId([
            'activity_group_id' => $activityGroupId,
            'code' => 'shalat-subuh',
            'name' => 'Shalat Subuh',
            'input_type' => 'checklist',
            'point_weight' => 10,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('student_activity_logs')->insert([
            [
                'school_id' => $schoolId,
                'student_id' => $student->id,
                'activity_item_id' => $activityItemId,
                'logged_on' => '2026-09-20',
                'status' => 'submitted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_id' => $schoolId,
                'student_id' => $student->id,
                'activity_item_id' => $activityItemId,
                'logged_on' => '2026-09-21',
                'status' => 'submitted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->artisan('app:lock-expired-activity-logs --date=2026-09-21')->assertExitCode(0);

        $this->assertDatabaseHas('student_activity_logs', [
            'student_id' => $student->id,
            'logged_on' => '2026-09-20',
            'status' => 'locked',
        ]);
        $this->assertDatabaseHas('student_activity_logs', [
            'student_id' => $student->id,
            'logged_on' => '2026-09-21',
            'status' => 'submitted',
        ]);
    }
}
