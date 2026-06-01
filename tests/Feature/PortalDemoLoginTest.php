<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalDemoLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_admin_login_redirects()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\PortalDemoSeeder']);

        $response = $this->post('/login', ['phone' => '0710000001', 'password' => 'admin123']);
        $response->assertRedirect('/admin/dashboard');
        $response->assertSessionHas('success');
    }

    public function test_login_requires_phone_and_password()
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['phone', 'password']);
    }
}
 