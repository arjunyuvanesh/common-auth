<?php

namespace Arjunyuvanesh\CommonAuth\Tests\Feature;

use Arjunyuvanesh\CommonAuth\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class AuthenticationTest extends TestCase
{
    // Automatically reset the in-memory SQLite database after every test
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure the Spatie role exists in the test database so registration doesn't crash
        Role::create(['name' => 'User', 'guard_name' => 'web']);
    }

    /** @test */
    public function a_user_can_register_via_api()
    {
        // Simulate a frontend sending a JSON POST request
        $response = $this->postJson('/common-auth/register', [
            'name'                  => 'Arjun',
            'email'                 => 'arjun@example.com',
            'mobile'                => '1234567890',
            'password'              => 'securePassword123!',
            'password_confirmation' => 'securePassword123!',
        ]);

        // Assert we get a 201 Created response
        $response->assertStatus(201)
                 ->assertJsonFragment(['success' => true]);

        // Assert the user was actually inserted into the database
        $this->assertDatabaseHas('users', [
            'email'  => 'arjun@example.com',
            'mobile' => '1234567890'
        ]);
    }

    /** @test */
    public function a_user_can_login_using_mobile_number_instead_of_email()
    {
        // 1. Create a user
        $this->postJson('/common-auth/register', [
            'name'                  => 'Arjun',
            'email'                 => 'arjun2@example.com',
            'mobile'                => '9876543210',
            'password'              => 'securePassword123!',
            'password_confirmation' => 'securePassword123!',
        ]);

        // 2. Attempt login using ONLY the mobile number (Testing our dynamic login logic!)
        $response = $this->postJson('/common-auth/login', [
            'login'    => '9876543210',
            'password' => 'securePassword123!'
        ]);

        // 3. Assert success
        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Successfully logged in.']);

        // 4. Assert Laravel formally recognizes the user as logged in
        $this->assertAuthenticated();
    }
}
