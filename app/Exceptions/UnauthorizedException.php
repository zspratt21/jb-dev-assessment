<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UnauthorizedException extends Exception
{
    public function render($request): JsonResponse
    {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
