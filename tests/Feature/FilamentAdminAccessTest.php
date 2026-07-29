<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_super_admin_cannot_access_filament_admin(): void
    {
        $admin = User::factory()->unverified()->create([
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => now()->timestamp])
            ->get('/admin');

        $response->assertRedirect();
        $this->assertStringContainsString('email-verification', $response->headers->get('Location') ?? '');
    }

    public function test_member_cannot_access_filament_admin(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($member)
            ->withSession(['admin_password_confirmed_at' => now()->timestamp])
            ->get('/admin');

        $response->assertRedirect(route('admin.forbidden'));
    }

    public function test_verified_super_admin_with_password_can_access_filament_admin(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => now()->timestamp])
            ->get('/admin');

        $response->assertOk();
    }
}
