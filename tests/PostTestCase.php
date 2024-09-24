<?php

namespace Tests;

use App\Models\User;

abstract class PostTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->other_user = User::factory()->create();
    }
}
