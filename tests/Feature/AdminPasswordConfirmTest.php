<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPasswordConfirmTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create([
            'role' => 'super_admin',
        ]);
    }

    public function test_admin_routes_redirect_to_password_confirm_without_session(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->get('/races-admin/races');

        $response->assertRedirect(route('admin.password.confirm'));
    }

    public function test_password_confirm_screen_renders_for_admin(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->get('/admin-confirm');

        $response->assertOk();
        $response->assertSee('관리자 확인');
    }

    public function test_correct_password_unlocks_admin_and_redirects(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->post('/admin-confirm', [
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
        $this->assertNotNull(session('admin_password_confirmed_at'));
    }

    public function test_wrong_password_is_rejected(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->from('/admin-confirm')->post('/admin-confirm', [
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/admin-confirm');
        $response->assertSessionHasErrors('password');
        $this->assertNull(session('admin_password_confirmed_at'));
    }

    public function test_confirmed_session_allows_admin_access(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => now()->timestamp])
            ->get('/races-admin/races');

        $response->assertOk();
    }

    public function test_member_cannot_open_admin_confirm(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($member)->get('/admin-confirm');

        $response->assertForbidden();
    }
}
