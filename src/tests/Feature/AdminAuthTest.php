<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_validation_error_when_email_is_missing()
    {
        $response = $this->post('/admin/login', [
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_shows_validation_error_when_password_is_missing()
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
        ]);

        $response->assertSessionHasErrors(['password']);
    }


    public function test_shows_validation_error_when_credentials_are_invalid()
    {
        $response = $this->post('/admin/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors();
    }
}