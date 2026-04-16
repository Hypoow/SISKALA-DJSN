<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeveloperPageTest extends TestCase
{
    public function test_guest_can_view_developer_page(): void
    {
        $response = $this->get(route('developer'));

        $response->assertOk();
        $response->assertSeeText('Developer yang merancang dan membuat aplikasi SISKALA.');
        $response->assertSee(route('login'));
    }

    public function test_login_page_contains_link_to_developer_page(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee(route('developer'));
        $response->assertSeeText('Lihat halaman developer SISKALA');
    }

    public function test_password_reset_request_page_contains_link_to_developer_page(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertOk();
        $response->assertSee(route('developer'));
        $response->assertSeeText('Lihat halaman developer SISKALA');
    }
}
