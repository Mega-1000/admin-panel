<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

trait ApiResponsesTrait
{
    protected function notFoundResponse($message = "Resource couldn't be found..."): JsonResponse
    {
        return response()->json($message, 404);
    }

    protected function createdResponse($message = null): JsonResponse
    {
        return response()->json($message, 201);
    }

    protected function createdErrorResponse($message): JsonResponse
    {
        return response()->json($message, 500);
    }

    protected function okResponse(): JsonResponse
    {
        return response()->json(null, 200);
    }
}
