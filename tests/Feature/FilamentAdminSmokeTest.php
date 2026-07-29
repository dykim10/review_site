<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

/**
 * 실DB 읽기 전용 스모크 (RefreshDatabase 없음).
 * 실행: 셸에서 DB_CONNECTION=pgsql 유지 후
 *   php artisan test --filter=FilamentAdminSmokeTest
 */
class FilamentAdminSmokeTest extends TestCase
{
    private function verifiedSuperAdmin(): User
    {
        $admin = User::query()
            ->where('role', 'super_admin')
            ->whereNotNull('email_verified_at')
            ->orderBy('id')
            ->first();

        $this->assertNotNull($admin, 'verified super_admin 필요');

        return $admin;
    }

    public function test_guest_admin_redirects_to_login(): void
    {
        $this->get('/admin')->assertRedirect();
    }

    public function test_super_admin_reaches_filament_dashboard(): void
    {
        $admin = $this->verifiedSuperAdmin();

        $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => now()->timestamp])
            ->get('/admin')
            ->assertOk();
    }

    public function test_super_admin_reaches_filament_races(): void
    {
        $admin = $this->verifiedSuperAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => now()->timestamp])
            ->get('/admin/races');

        $response->assertOk();
        $response->assertSee('대회', false);
    }

    public function test_super_admin_reaches_legacy_races_admin(): void
    {
        $admin = $this->verifiedSuperAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => now()->timestamp])
            ->get('/races-admin/races');

        $response->assertOk();
    }

    public function test_super_admin_without_password_goes_to_confirm(): void
    {
        $admin = $this->verifiedSuperAdmin();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertRedirect(route('admin.password.confirm'));
    }

    public function test_member_is_forbidden_from_filament(): void
    {
        $member = User::query()->where('role', 'member')->orderBy('id')->first();
        $this->assertNotNull($member, 'member 필요');

        $this->actingAs($member)
            ->withSession(['admin_password_confirmed_at' => now()->timestamp])
            ->get('/admin')
            ->assertRedirect(route('admin.forbidden'));
    }

    public function test_unverified_super_admin_is_sent_to_email_verification(): void
    {
        $admin = $this->verifiedSuperAdmin();
        $admin->email_verified_at = null;

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => now()->timestamp])
            ->get('/admin');

        $response->assertRedirect();
        $this->assertStringContainsString(
            'email-verification',
            $response->headers->get('Location') ?? ''
        );
    }
}
