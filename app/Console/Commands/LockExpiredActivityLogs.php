<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

#[Signature('app:lock-expired-activity-logs {--date= : Lock logs before this YYYY-MM-DD date}')]
#[Description('Lock activity reports that can no longer be changed')]
class LockExpiredActivityLogs extends Command
{
    public function handle(): int
    {
        $cutoffDate = $this->option('date')
            ? Carbon::parse((string) $this->option('date'))->toDateString()
            : now(config('app.timezone'))->toDateString();

        $lockedCount = DB::table('student_activity_logs')
            ->whereDate('logged_on', '<', $cutoffDate)
            ->whereIn('status', ['draft', 'submitted'])
            ->update(['status' => 'locked', 'updated_at' => now()]);

        $this->info("Locked {$lockedCount} activity reports.");

        return self::SUCCESS;
    }
}
