<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\ReportExportController;
use App\Livewire\Parent\Dashboard as ParentDashboard;
use App\Livewire\SchoolAdmin\AcademicYears as AdminAcademicYears;
use App\Livewire\SchoolAdmin\Classes as AdminClasses;
use App\Livewire\SchoolAdmin\Dashboard as AdminDashboard;
use App\Livewire\SchoolAdmin\Parents as AdminParents;
use App\Livewire\SchoolAdmin\Reports as AdminReports;
use App\Livewire\SchoolAdmin\Students as AdminStudents;
use App\Livewire\SchoolAdmin\Teachers as AdminTeachers;
use App\Livewire\Student\Dashboard as StudentDashboard;
use App\Livewire\Student\History as StudentHistory;
use App\Livewire\Student\Leaderboard as StudentLeaderboard;
use App\Livewire\Superadmin\Catalog as SuperadminCatalog;
use App\Livewire\Superadmin\Dashboard as SuperadminDashboard;
use App\Livewire\Superadmin\Reports as SuperadminReports;
use App\Livewire\Superadmin\Schools as SuperadminSchools;
use App\Livewire\Superadmin\Users as SuperadminUsers;
use App\Livewire\Teacher\ClassReport as TeacherClassReport;
use App\Livewire\Teacher\Dashboard as TeacherDashboard;
use App\Livewire\Teacher\Verification as TeacherVerification;
use Illuminate\Support\Facades\Route;

/* ── Guest routes ─────────────────────────────────────────────────────── */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

/* ── Authenticated routes ─────────────────────────────────────────────── */
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Root redirect based on role
    Route::get('/', [AuthenticatedSessionController::class, 'redirect'])->name('dashboard');

    /* ── Siswa ──────────────────────────────────────────────────────── */
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', StudentDashboard::class)->name('dashboard');
        Route::get('/history', StudentHistory::class)->name('history');
        Route::get('/leaderboard', StudentLeaderboard::class)->name('leaderboard');
    });

    /* ── Guru ───────────────────────────────────────────────────────── */
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', TeacherDashboard::class)->name('dashboard');
        Route::get('/verification', TeacherVerification::class)->name('verification');
        Route::get('/reports', TeacherClassReport::class)->name('reports');
    });

    /* ── Wali Murid ─────────────────────────────────────────────────── */
    Route::middleware('role:parent')->prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', ParentDashboard::class)->name('dashboard');
    });

    /* ── Admin Sekolah ──────────────────────────────────────────────── */
    Route::middleware('role:school_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/academic-years', AdminAcademicYears::class)->name('academic-years');
        Route::get('/classes', AdminClasses::class)->name('classes');
        Route::get('/teachers', AdminTeachers::class)->name('teachers');
        Route::get('/students', AdminStudents::class)->name('students');
        Route::get('/parents', AdminParents::class)->name('parents');
        Route::get('/reports', AdminReports::class)->name('reports');
    });

    /* ── Superadmin ─────────────────────────────────────────────────── */
    Route::middleware('role:superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', SuperadminDashboard::class)->name('dashboard');
        Route::get('/schools', SuperadminSchools::class)->name('schools');
        Route::get('/users', SuperadminUsers::class)->name('users');
        Route::get('/catalog', SuperadminCatalog::class)->name('catalog');
        Route::get('/reports', SuperadminReports::class)->name('reports');
    });

    /* ── Reports & Exports ─────────────────────────────────────────── */
    Route::get('/reports/export/csv', [ReportExportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('/reports/printable', [ReportExportController::class, 'printable'])->name('reports.printable');
});

/* ── Static asset: design-system CSS ─────────────────────────────────── */
Route::get('/amalin.css', function () {
    return response()->file(base_path('styles.css'), ['Content-Type' => 'text/css; charset=UTF-8']);
})->name('amalin.styles');

Route::get('/tarbiyah.css', function () {
    return response()->file(base_path('styles.css'), ['Content-Type' => 'text/css; charset=UTF-8']);
})->name('tarbiyah.styles');
