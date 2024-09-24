<?php

namespace App\Traits;

use App\Exceptions\ModelNotFoundException;
use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException as EloquentModelNotFoundException;

trait ChecksPostsExist
{
    /**
     * @throws ModelNotFoundException
     */
    public function checkPostExists($id): void
    {
        try {
            Post::findOrFail($id);
        } catch (EloquentModelNotFoundException $e) {
            throw new ModelNotFoundException("Post with id $id not found");
        }
    }
}
