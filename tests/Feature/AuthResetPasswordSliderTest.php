<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthResetPasswordSliderTest extends TestCase
{
    public function test_reset_password_page_uses_brand_slider_panel(): void
    {
        $response = $this->get(route('password.reset', [
            'token' => 'test-reset-token',
            'email' => 'tester@example.com',
        ]));

        $response->assertOk();
        $response->assertSee('data-auth-slider', false);
        $response->assertSee('logo-siskala-L.png', false);
        $response->assertSee('logo-djsn-L.png', false);
        $response->assertSeeInOrder([
            'SISKALA',
            'DJSN',
        ]);
    }
}
