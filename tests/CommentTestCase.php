<?php

namespace Tests;

use App\Models\Post;
use App\Models\User;

abstract class CommentTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->other_user = User::factory()->create();
        $this->post = Post::factory()->create(['title' => 'the first post', 'user_id' => $this->user->id]);
    }
}
