<?php

namespace Tests\Feature\Blog;

use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CommentTestCase;

class CommentTest extends CommentTestCase
{
    use RefreshDatabase;

    public function test_comment_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post("/api/posts/{$this->post->id}/comments", ['content' => 'testing testing 123']);
        $response->assertStatus(201);
        $this->assertNotNull(Comment::find($response->json('id')));
    }

    public function test_comment_cannot_be_created_on_non_existent_post(): void
    {
        $this->post->delete();
        $response = $this->actingAs($this->user)->post('/api/posts/1/comments', ['content' => 'testing testing 123']);
        $response->assertStatus(404);
        $response->assertJson(['error' => 'Post with id 1 not found']);
    }

    public function test_comment_cannot_be_created_by_guests(): void
    {
        $response = $this->post("/api/posts/{$this->post->id}/comments", ['content' => 'testing testing 123']);
        $response->assertStatus(401);
    }

    public function test_comments_can_be_retrieved_from_post(): void
    {
        Comment::factory()->count(20)->create(['post_id' => $this->post->id, 'user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get("/api/posts/{$this->post->id}/comments");
        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonStructure([
            'data' => [
                ['id', 'content', 'user_id', 'post_id', 'created_at', 'updated_at'],
            ],
        ]);
        $response->assertJsonFragment([
            'current_page' => 1,
            'total_pages' => 2,
        ]);
    }

    public function test_comments_cannot_be_retrieved_from_non_existent_post(): void
    {
        $this->post->delete();
        $response = $this->actingAs($this->user)->get("/api/posts/{$this->post->id}/comments");
        $response->assertStatus(404);
        $response->assertJson(['error' => "Post with id {$this->post->id} not found"]);
    }

    public function test_comments_can_be_retrieved_from_user(): void
    {
        Comment::factory()->count(20)->create(['user_id' => $this->user->id, 'post_id' => $this->post->id]);

        $response = $this->actingAs($this->user)->get("/api/users/{$this->user->id}/comments");
        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonStructure([
            'data' => [
                ['id', 'content', 'user_id', 'post_id', 'created_at', 'updated_at'],
            ],
        ]);
        $response->assertJsonFragment([
            'current_page' => 1,
            'total_pages' => 2,
        ]);
    }

    public function test_comments_cannot_be_retrieved_from_non_existent_user(): void
    {
        $response = $this->actingAs($this->user)->get('/api/users/999/comments');
        $response->assertStatus(404);
        $response->assertJson(['error' => 'User with id 999 not found']);
    }

    public function test_comment_can_be_updated(): void
    {
        $comment = Comment::factory()->create(['user_id' => $this->user->id, 'post_id' => $this->post->id]);
        $response = $this->actingAs($this->user)->patch("/api/comments/{$comment->id}", ['content' => 'testing testing 123']);
        $response->assertStatus(200);
        $this->assertEquals('testing testing 123', Comment::find($comment->id)->content);
    }

    public function test_comment_cannot_be_updated_by_guests(): void
    {
        $comment = Comment::factory()->create(['user_id' => $this->user->id, 'post_id' => $this->post->id]);
        $response = $this->patch("/api/comments/{$comment->id}", ['content' => 'testing testing 123']);
        $response->assertStatus(401);
    }

    public function test_comment_cannot_be_updated_by_other_users(): void
    {
        $comment = Comment::factory()->create(['post_id' => $this->post->id, 'user_id' => $this->other_user->id]);
        $response = $this->actingAs($this->user)->patch("/api/comments/{$comment->id}", ['content' => 'testing testing 123']);
        $response->assertStatus(404);
        $response->assertJson(['error' => "Comment with id {$comment->id} not found or is someone else's comment"]);
    }

    public function test_comment_can_be_deleted(): void
    {
        $comment = Comment::factory()->create(['user_id' => $this->user->id, 'post_id' => $this->post->id]);
        $response = $this->actingAs($this->user)->delete("/api/comments/{$comment->id}");
        $response->assertStatus(200);
        $response->assertJson(['message' => "Comment with id $comment->id deleted"]);
        $this->assertNull(Comment::find($comment->id));
    }

    public function test_comment_cannot_be_deleted_by_guests(): void
    {
        $comment = Comment::factory()->create(['user_id' => $this->user->id, 'post_id' => $this->post->id]);
        $response = $this->delete("/api/comments/{$comment->id}");
        $response->assertStatus(401);
    }

    public function test_comment_cannot_be_deleted_by_other_users(): void
    {
        $comment = Comment::factory()->create(['post_id' => $this->post->id, 'user_id' => $this->other_user->id]);
        $response = $this->actingAs($this->user)->delete("/api/comments/{$comment->id}");
        $response->assertStatus(404);
        $response->assertJson(['error' => "Comment with id {$comment->id} not found or is someone else's comment"]);
    }
}
