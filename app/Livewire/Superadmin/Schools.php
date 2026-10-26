<?php

namespace App\Livewire\Superadmin;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
#[Title('Kelola Sekolah — SIPENDEKARKU')]
class Schools extends Component
{
    public bool $showModal = false;

    public ?int $editingSchoolId = null;

    public string $code = '';

    public string $name = '';

    public string $address = '';

    public string $timezone = 'Asia/Jakarta';

    public bool $isActive = true;

    // School Admin fields (optional on create)
    public string $adminName = '';

    public string $adminEmail = '';

    public string $adminPassword = 'password';

    public ?string $message = null;

    public function openCreate(): void
    {
        $this->reset(['editingSchoolId', 'code', 'name', 'address', 'adminName', 'adminEmail']);
        $this->timezone = 'Asia/Jakarta';
        $this->isActive = true;
        $this->adminPassword = 'password';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $school = School::findOrFail($id);
        $this->editingSchoolId = $school->id;
        $this->code = $school->code;
        $this->name = $school->name;
        $this->address = $school->address ?? '';
        $this->timezone = $school->timezone;
        $this->isActive = $school->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'code' => 'required|string|max:20|unique:schools,code,'.$this->editingSchoolId,
            'name' => 'required|string|max:100',
            'address' => 'nullable|string',
            'timezone' => 'required|string|in:Asia/Jakarta,Asia/Makassar,Asia/Jayapura',
        ]);

        $school = School::updateOrCreate(
            ['id' => $this->editingSchoolId],
            [
                'code' => $this->code,
                'name' => $this->name,
                'address' => $this->address,
                'timezone' => $this->timezone,
                'is_active' => $this->isActive,
            ]
        );

        // If creating new school and admin info is provided, create initial structure & admin
        if (! $this->editingSchoolId && ! empty($this->adminEmail)) {
            $schoolAdminRole = Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);

            $adminUser = User::create([
                'school_id' => $school->id,
                'name' => $this->adminName ?: 'Admin '.$school->name,
                'email' => $this->adminEmail,
                'password' => Hash::make($this->adminPassword ?: 'password'),
            ]);
            $adminUser->syncRoles([$schoolAdminRole]);

            // Create default active academic year and semester
            $academicYear = AcademicYear::create([
                'school_id' => $school->id,
                'name' => '2026/2027',
                'starts_on' => '2026-07-14',
                'ends_on' => '2027-06-20',
                'is_active' => true,
            ]);

            Semester::create([
                'academic_year_id' => $academicYear->id,
                'name' => 'Ganjil',
                'starts_on' => '2026-07-14',
                'ends_on' => '2026-12-20',
                'is_active' => true,
            ]);
        }

        $this->showModal = false;
        $this->message = "Data sekolah {$school->name} berhasil disimpan.";
    }

    public function toggleActive(int $id): void
    {
        $school = School::findOrFail($id);
        $school->update(['is_active' => ! $school->is_active]);
        $status = $school->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->message = "Sekolah {$school->name} telah {$status}.";
    }

    public function render()
    {
        $schools = School::withCount(['users', 'classes', 'academicYears'])
            ->orderBy('name')
            ->get();

        return view('livewire.superadmin.schools', [
            'schools' => $schools,
        ]);
    }
}
