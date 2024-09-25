<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;

class ModelNotFoundException extends ApiExpectedException
{
    public function render($request): JsonResponse
    {
        return response()->json(['error' => $this->getMessage()], 404);
    }
}
