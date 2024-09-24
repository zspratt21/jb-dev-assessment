<?php

namespace Tests\Feature\Blog;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_can_be_created()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/api/posts', ['title' => 'the first post', 'content' => 'testing testing 123']);
        $response->assertStatus(201);
        $this->assertNotNull(Post::find($response->json('id')));
    }

    public function test_post_cannot_be_created_by_guests()
    {
        $response = $this->post('/api/posts', ['title' => 'the first post', 'content' => 'testing testing 123']);
        $response->assertStatus(401);
    }

    public function test_post_cannot_be_created_with_existing_title()
    {
        $user = User::factory()->create();
        Post::factory()->create(['title' => 'the first post', 'user_id' => $user->id]);
        $response = $this->actingAs($user)->post('/api/posts', ['title' => 'the first post', 'content' => 'testing testing 123']);
        $response->assertStatus(400);
        $response->assertJson(['error' => 'Post with title `the first post` already exists']);
    }

    public function test_post_can_be_retrieved()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['title' => 'the first post', 'user_id' => $user->id]);
        $response = $this->actingAs($user)->get("/api/posts/{$post->id}");
        $response->assertStatus(200);
        $response->assertJson(['id' => $post->id, 'title' => 'the first post', 'content' => $post->content]);
    }

    public function test_post_cannot_be_retrieved_by_guests()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['title' => 'the first post', 'user_id' => $user->id]);
        $response = $this->get("/api/posts/{$post->id}");
        $response->assertStatus(401);
    }

    public function test_posts_can_be_listed()
    {
        $user = User::factory()->create();
        $post1 = Post::factory()->create(['title' => 'the first post', 'user_id' => $user->id]);
        $post2 = Post::factory()->create(['title' => 'the second post', 'user_id' => $user->id]);
        $response = $this->actingAs($user)->get('/api/posts');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['id' => $post1->id, 'title' => 'the first post', 'content' => $post1->content],
                ['id' => $post2->id, 'title' => 'the second post', 'content' => $post2->content],
            ],
            'current_page' => 1,
            'total_pages' => 1,
        ]);
    }

    public function test_post_can_be_updated()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['title' => 'the first post', 'user_id' => $user->id]);
        $post->save();
        $response = $this->actingAs($user)->patch("/api/posts/{$post->id}", ['content' => 'testing testing 123']);
        $response->assertStatus(200);
        $this->assertEquals('testing testing 123', Post::find($post->id)->content);
        $this->assertEquals('the first post', Post::find($post->id)->title);
    }

    public function test_post_cannot_be_updated_by_guests()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $response = $this->patch("/api/posts/{$post->id}", ['content' => 'testing testing 123']);
        $response->assertStatus(401);
    }

    public function test_post_cannot_be_updated_by_other_users()
    {
        $post_user = User::factory()->create();
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $post_user->id]);
        $response = $this->actingAs($user)->patch("/api/posts/{$post->id}", ['content' => 'testing testing 123']);
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthorized']);
    }

    public function test_post_can_be_deleted()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->delete("/api/posts/{$post->id}");
        $response->assertStatus(200);
        $response->assertJson(['message' => "Post with $post->id deleted"]);
        $this->assertNull(Post::find($post->id));
    }

    public function test_post_cannot_be_deleted_by_guests()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $response = $this->delete("/api/posts/{$post->id}");
        $response->assertStatus(401);
    }

    public function test_post_cannot_be_deleted_by_other_users()
    {
        $post_user = User::factory()->create();
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $post_user->id]);
        $response = $this->actingAs($user)->delete("/api/posts/{$post->id}");
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthorized']);
    }
}
