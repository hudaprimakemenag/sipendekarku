<?php

namespace App\Livewire\Teacher;

use App\Models\ActivityVerification;
use App\Models\SchoolClass;
use App\Models\StudentActivityLog;
use App\Models\StudentPoint;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Verifikasi Kegiatan — AMALIN')]
class Verification extends Component
{
    public ?int $selectedClassId = null;

    public string $selectedDate = '';

    public string $selectedStudentId = 'all';

    /** @var array<int, string> */
    public array $notes = [];

    public ?string $feedbackMessage = null;

    public function mount(?int $class_id = null): void
    {
        $teacher = Auth::user();
        $this->selectedDate = request()->query('date', Carbon::today()->toDateString());

        $classes = $teacher->currentTeachingClasses();
        $this->selectedClassId = $class_id ?? $classes->first()?->id;
    }

    public function approve(int $logId): void
    {
        $log = StudentActivityLog::with('activityItem')->find($logId);
        if (! $log) {
            return;
        }

        $note = $this->notes[$logId] ?? null;
        $points = $log->activityItem?->point_weight ?? 0;

        $verification = ActivityVerification::updateOrCreate(
            ['student_activity_log_id' => $log->id],
            [
                'teacher_id' => Auth::id(),
                'status' => 'approved',
                'notes' => $note,
                'credited_points' => $points,
                'verified_at' => now(),
            ]
        );

        StudentPoint::updateOrCreate(
            ['activity_verification_id' => $verification->id],
            [
                'school_id' => $log->school_id,
                'student_id' => $log->student_id,
                'semester_id' => $log->semester_id,
                'points' => $points,
                'description' => $log->activityItem?->name ?? 'Kegiatan Terverifikasi',
                'credited_at' => now(),
            ]
        );

        $this->feedbackMessage = 'Laporan kegiatan siswa berhasil disetujui & poin telah dikreditkan.';
    }

    public function reject(int $logId): void
    {
        $log = StudentActivityLog::find($logId);
        if (! $log) {
            return;
        }

        $note = $this->notes[$logId] ?? 'Tidak memenuhi kriteria kegiatan.';

        $verification = ActivityVerification::updateOrCreate(
            ['student_activity_log_id' => $log->id],
            [
                'teacher_id' => Auth::id(),
                'status' => 'rejected',
                'notes' => $note,
                'credited_points' => 0,
                'verified_at' => now(),
            ]
        );

        StudentPoint::where('activity_verification_id', $verification->id)->delete();

        $this->feedbackMessage = 'Laporan kegiatan ditandai tidak disetujui.';
    }

    public function approveAllFilled(): void
    {
        if (! $this->selectedClassId) {
            return;
        }

        $query = StudentActivityLog::where('class_id', $this->selectedClassId)
            ->whereDate('logged_on', $this->selectedDate)
            ->where(function ($q) {
                $q->where('value', '1')->orWhere('value', '!=', '');
            })
            ->with('activityItem');

        if ($this->selectedStudentId !== 'all') {
            $query->where('student_id', (int) $this->selectedStudentId);
        }

        $logs = $query->get();
        $count = 0;

        foreach ($logs as $log) {
            $points = $log->activityItem?->point_weight ?? 0;

            $verification = ActivityVerification::updateOrCreate(
                ['student_activity_log_id' => $log->id],
                [
                    'teacher_id' => Auth::id(),
                    'status' => 'approved',
                    'notes' => $this->notes[$log->id] ?? null,
                    'credited_points' => $points,
                    'verified_at' => now(),
                ]
            );

            StudentPoint::updateOrCreate(
                ['activity_verification_id' => $verification->id],
                [
                    'school_id' => $log->school_id,
                    'student_id' => $log->student_id,
                    'semester_id' => $log->semester_id,
                    'points' => $points,
                    'description' => $log->activityItem?->name ?? 'Kegiatan Terverifikasi',
                    'credited_at' => now(),
                ]
            );

            $count++;
        }

        $this->feedbackMessage = "Berhasil menyetujui {$count} kegiatan yang terisi sekaligus.";
    }

    public function render()
    {
        $teacher = Auth::user();
        $classes = $teacher->currentTeachingClasses();

        $currentClass = $this->selectedClassId
            ? SchoolClass::with('classStudents.student')->find($this->selectedClassId)
            : null;

        $students = $currentClass?->classStudents?->whereNull('left_at')->pluck('student')->filter() ?? collect();

        // Get student logs for selected class & date
        $logsQuery = StudentActivityLog::where('class_id', $this->selectedClassId)
            ->whereDate('logged_on', $this->selectedDate)
            ->with(['student', 'activityItem.activityGroup', 'verification.teacher']);

        if ($this->selectedStudentId !== 'all') {
            $logsQuery->where('student_id', (int) $this->selectedStudentId);
        }

        $logs = $logsQuery->get()->groupBy('student_id');

        // Pre-fill notes from database
        foreach ($logs as $studentLogs) {
            foreach ($studentLogs as $l) {
                if (! isset($this->notes[$l->id]) && $l->verification?->notes) {
                    $this->notes[$l->id] = $l->verification->notes;
                }
            }
        }

        return view('livewire.teacher.verification', [
            'classes' => $classes,
            'currentClass' => $currentClass,
            'students' => $students,
            'groupedLogs' => $logs,
        ]);
    }
}
