<?php

namespace Tests\Unit;

use Filament\Facades\Filament;
use Tests\TestCase;

class FilamentAdminPanelConfigTest extends TestCase
{
    public function test_admin_panel_requires_email_verification(): void
    {
        $panel = Filament::getPanel('admin');

        $this->assertTrue($panel->hasEmailVerification());
        $this->assertTrue($panel->isEmailVerificationRequired());
        $this->assertStringStartsWith('verified:', $panel->getEmailVerifiedMiddleware());
    }
}
