<?php

namespace tests\feature;

use tests\TestCase;
use app\models\User;
use app\fixtures\UserFixture;

/**
 * Feature tests for authentication functionality
 */
class AuthenticationTest extends TestCase
{
    /**
     * @return array list of fixtures to be loaded
     */
    public function fixtures(): array
    {
        return [
            'users' => UserFixture::class,
        ];
    }

    /**
     * Test user registration with valid data
     */
    public function testUserCanRegisterWithValidData(): void
    {
        $response = $this->mockWebApplication()->handleRequest('/register', 'POST', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        // Check redirect after successful registration
        $this->assertEquals(302, $response->statusCode);
        
        // Check user exists in database
        $user = User::findOne(['email' => 'test@example.com']);
        $this->assertNotNull($user);
    }

    /**
     * Test user cannot register with invalid data
     */
    public function testUserCannotRegisterWithInvalidData(): void
    {
        $response = $this->mockWebApplication()->handleRequest('/register', 'POST', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        // Should have validation errors
        $this->assertEquals(422, $response->statusCode);
        
        // No user should be created
        $user = User::findOne(['email' => 'not-an-email']);
        $this->assertNull($user);
    }

    /**
     * Test user can login with valid credentials
     */
    public function testUserCanLoginWithValidCredentials(): void
    {
        $user = $this->users[0];
        
        $response = $this->mockWebApplication()->handleRequest('/login', 'POST', [
            'email' => $user->email,
            'password' => 'password123', // Default password from fixture
        ]);

        // Check redirect after successful login
        $this->assertEquals(302, $response->statusCode);
    }

    /**
     * Test user cannot login with wrong password
     */
    public function testUserCannotLoginWithWrongPassword(): void
    {
        $user = $this->users[0];
        
        $response = $this->mockWebApplication()->handleRequest('/login', 'POST', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        // Should have validation errors
        $this->assertEquals(422, $response->statusCode);
    }

    /**
     * Test user can logout
     */
    public function testUserCanLogout(): void
    {
        $user = $this->users[0];
        
        // First login
        $this->mockWebApplication()->handleRequest('/login', 'POST', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        // Then logout
        $response = $this->mockWebApplication()->handleRequest('/logout', 'POST');

        // Check redirect after logout
        $this->assertEquals(302, $response->statusCode);
    }
}
