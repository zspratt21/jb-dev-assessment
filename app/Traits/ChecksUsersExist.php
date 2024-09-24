<?php

namespace App\Traits;

use App\Exceptions\ModelNotFoundException;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException as EloquentModelNotFoundException;

trait ChecksUsersExist
{
    /**
     * @throws ModelNotFoundException
     */
    public function checkUserExists($id): void
    {
        try {
            User::findOrFail($id);
        } catch (EloquentModelNotFoundException $e) {
            throw new ModelNotFoundException("User with id $id not found");
        }
    }
}
