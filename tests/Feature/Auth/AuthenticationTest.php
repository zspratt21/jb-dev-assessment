<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Override the APP_URL environment variable
        config(['app.url' => 'http://web:80']);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        //        $this->logResponseToFile($response);
        dump('Full URL: '.$this->app['request']->fullUrl());

        $this->assertAuthenticated();
        $response->assertNoContent();
    }

    private function logResponseToFile(TestResponse $response): void
    {
        $logPath = storage_path('logs/test_response.html');
        File::put($logPath, $response->getContent());
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();

        dump(config('app.url'));

        //        $response->dump();

        $response->assertNoContent();
    }
}
