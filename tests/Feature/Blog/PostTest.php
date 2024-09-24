<?php

namespace Tests\Feature\Blog;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\PostTestCase;

class PostTest extends PostTestCase
{
    use RefreshDatabase;

    public function test_post_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post('/api/posts', ['title' => 'the first post', 'content' => 'testing testing 123']);
        $response->assertStatus(201);
        $this->assertNotNull(Post::find($response->json('id')));
    }

    public function test_post_cannot_be_created_by_guests(): void
    {
        $response = $this->post('/api/posts', ['title' => 'the first post', 'content' => 'testing testing 123']);
        $response->assertStatus(401);
    }

    public function test_post_cannot_be_created_with_existing_title(): void
    {
        Post::factory()->create(['title' => 'the first post', 'user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->post('/api/posts', ['title' => 'the first post', 'content' => 'testing testing 123']);
        $response->assertStatus(400);
        $response->assertJson(['error' => 'Post with title `the first post` already exists']);
    }

    public function test_post_can_be_retrieved(): void
    {
        $post = Post::factory()->create(['title' => 'the first post', 'user_id' => $this->user->id]);
        $response = $this->actingAs($this->user)->get("/api/posts/{$post->id}");
        $response->assertStatus(200);
        $response->assertJson(['id' => $post->id, 'title' => 'the first post', 'content' => $post->content]);
    }

    public function test_post_cannot_be_retrieved_by_guests(): void
    {
        $post = Post::factory()->create(['title' => 'the first post', 'user_id' => $this->user->id]);
        $response = $this->get("/api/posts/{$post->id}");
        $response->assertStatus(401);
    }

    public function test_posts_can_be_listed(): void
    {
        Post::factory()->count(20)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get('/api/posts');
        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonStructure([
            'data' => [
                ['id', 'title', 'user_id', 'content', 'created_at', 'updated_at'],
            ],
        ]);
        $response->assertJsonFragment([
            'current_page' => 1,
            'total_pages' => 2,
        ]);
    }

    public function test_post_can_be_updated(): void
    {
        $post = Post::factory()->create(['title' => 'the first post', 'user_id' => $this->user->id]);
        $post->save();
        $response = $this->actingAs($this->user)->patch("/api/posts/{$post->id}", ['content' => 'testing testing 123']);
        $response->assertStatus(200);
        $this->assertEquals('testing testing 123', Post::find($post->id)->content);
        $this->assertEquals('the first post', Post::find($post->id)->title);
    }

    public function test_post_cannot_be_updated_by_guests(): void
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $response = $this->patch("/api/posts/{$post->id}", ['content' => 'testing testing 123']);
        $response->assertStatus(401);
    }

    public function test_post_cannot_be_updated_by_other_users(): void
    {
        $post = Post::factory()->create(['user_id' => $this->other_user->id]);
        $response = $this->actingAs($this->user)->patch("/api/posts/{$post->id}", ['content' => 'testing testing 123']);
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthorized']);
    }

    public function test_post_can_be_deleted(): void
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        Comment::factory()->create(['user_id' => $this->user->id, 'post_id' => $post->id]);
        $this->assertCount(1, Comment::all());
        $response = $this->actingAs($this->user)->delete("/api/posts/{$post->id}");
        $response->assertStatus(200);
        $response->assertJson(['message' => "Post with $post->id deleted"]);
        $this->assertNull(Post::find($post->id));
        $this->assertCount(0, Comment::all());
    }

    public function test_post_cannot_be_deleted_by_guests(): void
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $response = $this->delete("/api/posts/{$post->id}");
        $response->assertStatus(401);
    }

    public function test_post_cannot_be_deleted_by_other_users(): void
    {
        $post = Post::factory()->create(['user_id' => $this->other_user->id]);
        $response = $this->actingAs($this->user)->delete("/api/posts/{$post->id}");
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthorized']);
    }
}
