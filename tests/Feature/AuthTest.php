<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $resp = $this->postJson('/api/register', $payload);

        $resp->assertStatus(201)
             ->assertJsonStructure(['user', 'token']);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_login_and_logout()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $login = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $login->assertStatus(200)->assertJsonStructure(['token']);

        // authenticate for logout
        $token = $login->json('token');
        Sanctum::actingAs($user, ['*']);

        $this->postJson('/api/logout')->assertStatus(200);
    }
}
