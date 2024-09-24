<?php

namespace Tests\Feature\Blog;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_retireved(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/api/users/'.$user->id);
        $response->assertStatus(200);
        $response->assertJson(['id' => $user->id, 'name' => $user->name, 'email' => $user->email]);
    }

    public function test_user_cannot_be_retrieved_by_guests(): void
    {
        $user = User::factory()->create();
        $response = $this->get('/api/users/'.$user->id);
        $response->assertStatus(401);
    }

    public function test_users_can_be_listed(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $response = $this->actingAs($user1)->get('/api/users');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['id' => $user1->id, 'name' => $user1->name, 'email' => $user1->email],
                ['id' => $user2->id, 'name' => $user2->name, 'email' => $user2->email],
            ],
            'current_page' => 1,
            'total_pages' => 1,
        ]);
    }
}
