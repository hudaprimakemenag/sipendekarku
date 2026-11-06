<?php

namespace Tests\Feature;

use App\Livewire\Auth\ChangePassword;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
    }

    public function test_topbar_renders_user_menu_with_initials_and_logout_button(): void
    {
        $school = School::create(['code' => 'SCH-TEST', 'name' => 'SDIT Test', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $user = User::factory()->create([
            'school_id' => $school->id,
            'name' => 'Fulan Ahmad',
            'email' => 'fulan@sekolah.id',
            'password' => Hash::make('password123'),
        ]);
        $user->syncRoles(['teacher']);

        $response = $this->actingAs($user)->get(route('teacher.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('FU'); // Initials of Fulan Ahmad (substr 0, 2)
        $response->assertSee('Ubah Kata Sandi');
        $response->assertSee('Keluar (Logout)');
    }

    public function test_user_can_change_password_successfully(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password-123'),
        ]);
        $user->syncRoles(['student']);

        $this->actingAs($user);

        Livewire::test(ChangePassword::class)
            ->call('openModal')
            ->assertSet('showModal', true)
            ->set('currentPassword', 'old-password-123')
            ->set('newPassword', 'new-secret-456')
            ->set('newPasswordConfirmation', 'new-secret-456')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertSet('successMessage', 'Kata sandi Anda berhasil diperbarui di database!');

        $user->refresh();
        $this->assertTrue(Hash::check('new-secret-456', $user->password));
        $this->assertFalse(Hash::check('old-password-123', $user->password));

        // Test logging in with the new password
        $this->post('/logout');
        $this->assertGuest();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'new-secret-456',
        ]);
        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_password_change_fails_with_incorrect_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
        ]);

        $this->actingAs($user);

        Livewire::test(ChangePassword::class)
            ->set('currentPassword', 'wrong-password')
            ->set('newPassword', 'brand-new-pwd-123')
            ->set('newPasswordConfirmation', 'brand-new-pwd-123')
            ->call('updatePassword')
            ->assertHasErrors(['currentPassword']);

        $user->refresh();
        $this->assertTrue(Hash::check('correct-password', $user->password));
    }

    public function test_password_change_fails_when_confirmation_does_not_match(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('current-pass'),
        ]);

        $this->actingAs($user);

        Livewire::test(ChangePassword::class)
            ->set('currentPassword', 'current-pass')
            ->set('newPassword', 'new-password-789')
            ->set('newPasswordConfirmation', 'different-password')
            ->call('updatePassword')
            ->assertHasErrors(['newPasswordConfirmation']);
    }

    public function test_password_change_fails_when_new_password_is_same_as_current(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('same-password'),
        ]);

        $this->actingAs($user);

        Livewire::test(ChangePassword::class)
            ->set('currentPassword', 'same-password')
            ->set('newPassword', 'same-password')
            ->set('newPasswordConfirmation', 'same-password')
            ->call('updatePassword')
            ->assertHasErrors(['newPassword']);
    }

    public function test_user_can_logout_via_logout_route(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
