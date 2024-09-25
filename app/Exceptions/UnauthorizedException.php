<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;

class UnauthorizedException extends ApiExpectedException
{
    public function render($request): JsonResponse
    {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
