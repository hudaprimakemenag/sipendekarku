<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_dashboard_renders_for_an_authenticated_user(): void
    {
        $role = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $user = User::factory()->create([
            'name' => 'Naila Azzahra',
        ]);
        $user->syncRoles([$role]);

        $response = $this->actingAs($user)->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Mutaba\'ah Hari Ini');
        $response->assertSee('Naila Azzahra');
    }
}
