<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure(['token', 'user']);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['token', 'user']);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
        ]);

        $login = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $token = $login->json('token');
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/logout');

        $response->assertOk()->assertJson(['message' => 'Logged out']);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}