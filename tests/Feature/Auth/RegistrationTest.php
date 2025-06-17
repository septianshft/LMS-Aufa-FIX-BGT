<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        // Ensure roles exist for testing
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'trainee']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'talent']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'recruiter']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'pekerjaan' => 'Software Developer',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'trainee',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
