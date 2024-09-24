<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelNotFoundException;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException as EloquentModelNotFoundException;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * @throws ModelNotFoundException
     */
    public function checkUserExists($id)
    {
        try {
            User::findOrFail($id);
        } catch (EloquentModelNotFoundException $e) {
            throw new ModelNotFoundException("User with id $id not found");
        }
    }

    public function index(): JsonResponse
    {
        return response()->json(User::all());
    }

    /**
     * @throws ModelNotFoundException
     */
    public function show(int $id): JsonResponse
    {
        $this->checkUserExists($id);

        return response()->json(User::find($id));
    }
}
