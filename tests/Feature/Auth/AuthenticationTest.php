<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertJson([
            'access_token' => true,
            'token_type' => 'Bearer',
        ]);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertJsonMissing([
            'access_token',
            'token_type' => 'Bearer',
        ]);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();
        $user->createToken('auth_token')->plainTextToken;
        $this->assertCount(1, $user->tokens);

        $response = $this->actingAs($user)->post('/api/logout');
        $response->assertNoContent();

        $user->refresh();
        $this->assertCount(0, $user->tokens);
    }

}
