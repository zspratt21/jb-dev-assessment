<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelNotFoundException;
use App\Models\User;
use App\Traits\ChecksUsersExist;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use ChecksUsersExist;

    public function index(): JsonResponse
    {
        $users = User::paginate(10);

        return response()->json([
            'data' => $users->items(),
            'current_page' => $users->currentPage(),
            'total_pages' => $users->lastPage(),
        ]);
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
