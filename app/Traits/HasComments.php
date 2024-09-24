<?php

namespace App\Traits;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasComments
{
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
