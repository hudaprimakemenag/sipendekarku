<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\StudentActivityLog;
use App\Models\StudentPoint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    /**
     * Export reports to CSV (Excel compatible).
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $type = $request->query('type', 'student');
        $filename = "laporan_{$type}_".now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($type, $request) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            match ($type) {
                'student' => $this->exportStudentCsv($handle, $request),
                'class' => $this->exportClassCsv($handle, $request),
                'school' => $this->exportSchoolCsv($handle, $request),
                'global' => $this->exportGlobalCsv($handle, $request),
                default => null,
            };

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Printable HTML / PDF View.
     */
    public function printable(Request $request)
    {
        $type = $request->query('type', 'student');
        $user = Auth::user();

        return match ($type) {
            'student' => $this->renderStudentPrintable($request),
            'class' => $this->renderClassPrintable($request),
            'school' => $this->renderSchoolPrintable($request),
            'global' => $this->renderGlobalPrintable($request),
            default => abort(404),
        };
    }

    private function exportStudentCsv($handle, Request $request): void
    {
        $studentId = $request->query('student_id', Auth::id());
        $student = User::with('school')->findOrFail($studentId);

        fputcsv($handle, ['LAPORAN MUTABA\'AH KEGIATAN HARIAN SISWA']);
        fputcsv($handle, ['Nama Siswa', $student->name]);
        fputcsv($handle, ['NISN', $student->student_number ?? '-']);
        fputcsv($handle, ['Sekolah', $student->school?->name ?? '-']);
        fputcsv($handle, ['Tanggal Cetak', now()->translatedFormat('d F Y H:i')]);
        fputcsv($handle, []);

        fputcsv($handle, ['Tanggal', 'Kegiatan', 'Kelompok', 'Laporan Siswa', 'Status Verifikasi', 'Catatan Guru', 'Poin']);

        $logs = StudentActivityLog::where('student_id', $student->id)
            ->with(['activityItem.activityGroup', 'verification.teacher'])
            ->orderByDesc('logged_on')
            ->get();

        foreach ($logs as $log) {
            $item = $log->activityItem;
            $v = $log->verification;

            $statusText = match ($v?->status) {
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                default => 'Menunggu',
            };

            $valText = $item?->input_type === 'checklist'
                ? ($log->value === '1' ? 'Terlaksana' : 'Tidak')
                : ($log->value ?: '-');

            fputcsv($handle, [
                $log->logged_on->format('Y-m-d'),
                $item?->name ?? '-',
                $item?->activityGroup?->name ?? '-',
                $valText,
                $statusText,
                $v?->notes ?? '-',
                $v && $v->status === 'approved' ? $v->credited_points : '0',
            ]);
        }
    }

    private function exportClassCsv($handle, Request $request): void
    {
        $classId = $request->query('class_id');
        $class = SchoolClass::with(['school', 'classStudents.student'])->findOrFail($classId);

        fputcsv($handle, ['REKAPITULASI LAPORAN KELAS']);
        fputcsv($handle, ['Kelas', $class->name]);
        fputcsv($handle, ['Sekolah', $class->school?->name ?? '-']);
        fputcsv($handle, ['Tanggal Cetak', now()->translatedFormat('d F Y H:i')]);
        fputcsv($handle, []);

        fputcsv($handle, ['Peringkat', 'Nama Siswa', 'NISN', 'Total Poin Terverifikasi']);

        $students = $class->classStudents->whereNull('left_at')->pluck('student')->filter();
        $ranked = [];
        foreach ($students as $st) {
            $ranked[] = [
                'student' => $st,
                'points' => $st->totalVerifiedPoints(),
            ];
        }
        usort($ranked, fn ($a, $b) => $b['points'] <=> $a['points']);

        $rank = 1;
        foreach ($ranked as $r) {
            fputcsv($handle, [
                $rank++,
                $r['student']->name,
                $r['student']->student_number ?? '-',
                $r['points'],
            ]);
        }
    }

    private function exportSchoolCsv($handle, Request $request): void
    {
        $school = Auth::user()->school;
        fputcsv($handle, ['LAPORAN AKUMULASI POIN SEKOLAH']);
        fputcsv($handle, ['Sekolah', $school?->name ?? '-']);
        fputcsv($handle, ['Tanggal Cetak', now()->translatedFormat('d F Y H:i')]);
        fputcsv($handle, []);

        fputcsv($handle, ['Peringkat', 'Nama Siswa', 'NISN', 'Kelas', 'Total Poin Terverifikasi']);

        $students = User::role('student')->where('school_id', $school?->id)->get();
        $ranked = [];
        foreach ($students as $st) {
            $ranked[] = [
                'student' => $st,
                'class' => $st->currentClass()?->name ?? '-',
                'points' => $st->totalVerifiedPoints(),
            ];
        }
        usort($ranked, fn ($a, $b) => $b['points'] <=> $a['points']);

        $rank = 1;
        foreach ($ranked as $r) {
            fputcsv($handle, [
                $rank++,
                $r['student']->name,
                $r['student']->student_number ?? '-',
                $r['class'],
                $r['points'],
            ]);
        }
    }

    private function exportGlobalCsv($handle, Request $request): void
    {
        fputcsv($handle, ['LAPORAN KOMPARASI LINTAS SEKOLAH (SUPERADMIN)']);
        fputcsv($handle, ['Tanggal Cetak', now()->translatedFormat('d F Y H:i')]);
        fputcsv($handle, []);

        fputcsv($handle, ['Peringkat', 'Sekolah', 'Kode', 'Jumlah Siswa', 'Jumlah Guru', 'Aktivitas Terisi', 'Total Poin']);

        $schools = School::all();
        $summaries = [];
        foreach ($schools as $sch) {
            $stCount = User::role('student')->where('school_id', $sch->id)->count();
            $tcCount = User::role('teacher')->where('school_id', $sch->id)->count();
            $logCount = StudentActivityLog::where('school_id', $sch->id)->where(function ($q) {
                $q->where('value', '1')->orWhere('value', '!=', '');
            })->count();
            $ptSum = StudentPoint::where('school_id', $sch->id)->sum('points');

            $summaries[] = [
                'school' => $sch,
                'students' => $stCount,
                'teachers' => $tcCount,
                'logs' => $logCount,
                'points' => $ptSum,
            ];
        }
        usort($summaries, fn ($a, $b) => $b['points'] <=> $a['points']);

        $rank = 1;
        foreach ($summaries as $s) {
            fputcsv($handle, [
                $rank++,
                $s['school']->name,
                $s['school']->code,
                $s['students'],
                $s['teachers'],
                $s['logs'],
                $s['points'],
            ]);
        }
    }

    private function renderStudentPrintable(Request $request)
    {
        $studentId = $request->query('student_id', Auth::id());
        $student = User::with('school')->findOrFail($studentId);

        $logs = StudentActivityLog::where('student_id', $student->id)
            ->with(['activityItem.activityGroup', 'verification.teacher'])
            ->orderByDesc('logged_on')
            ->take(30)
            ->get();

        return view('reports.printable', [
            'title' => 'Raport Mutaba\'ah Siswa',
            'school' => $student->school,
            'student' => $student,
            'logs' => $logs,
            'type' => 'student',
        ]);
    }

    private function renderClassPrintable(Request $request)
    {
        $classId = $request->query('class_id');
        $class = SchoolClass::with(['school', 'classStudents.student'])->findOrFail($classId);

        $students = $class->classStudents->whereNull('left_at')->pluck('student')->filter();
        $ranked = [];
        foreach ($students as $st) {
            $ranked[] = [
                'student' => $st,
                'points' => $st->totalVerifiedPoints(),
            ];
        }
        usort($ranked, fn ($a, $b) => $b['points'] <=> $a['points']);

        return view('reports.printable', [
            'title' => 'Laporan Rekapitulasi Kelas '.$class->name,
            'school' => $class->school,
            'class' => $class,
            'ranked' => $ranked,
            'type' => 'class',
        ]);
    }

    private function renderSchoolPrintable(Request $request)
    {
        $school = Auth::user()->school ?? School::first();

        $students = User::role('student')->where('school_id', $school?->id)->get();
        $ranked = [];
        foreach ($students as $st) {
            $ranked[] = [
                'student' => $st,
                'class' => $st->currentClass()?->name ?? '-',
                'points' => $st->totalVerifiedPoints(),
            ];
        }
        usort($ranked, fn ($a, $b) => $b['points'] <=> $a['points']);

        return view('reports.printable', [
            'title' => 'Laporan Akumulasi Prestasi Sekolah',
            'school' => $school,
            'ranked' => $ranked,
            'type' => 'school',
        ]);
    }

    private function renderGlobalPrintable(Request $request)
    {
        $schools = School::all();
        $summaries = [];
        foreach ($schools as $sch) {
            $stCount = User::role('student')->where('school_id', $sch->id)->count();
            $logCount = StudentActivityLog::where('school_id', $sch->id)->where(function ($q) {
                $q->where('value', '1')->orWhere('value', '!=', '');
            })->count();
            $ptSum = StudentPoint::where('school_id', $sch->id)->sum('points');

            $summaries[] = [
                'school' => $sch,
                'students' => $stCount,
                'logs' => $logCount,
                'points' => $ptSum,
            ];
        }
        usort($summaries, fn ($a, $b) => $b['points'] <=> $a['points']);

        return view('reports.printable', [
            'title' => 'Laporan Komparasi Multi-Sekolah',
            'school' => null,
            'summaries' => $summaries,
            'type' => 'global',
        ]);
    }
}
