<?php
/**
 * Author: Sebastian Rogala
 * Mail: sebrogala@gmail.com
 * Created: 04.01.2019
 */

namespace App\Http\Controllers\Api;

trait ApiResponsesTrait
{
    protected function notFoundResponse($message = "Resource couldn't be found...")
    {
        return response()->make($message, 404);
    }

    protected function createdResponse()
    {
        return response()->make(null, 201);
    }

    protected function createdErrorResponse($message)
    {
        return response()->make($message, 500);
    }

    protected function okResponse()
    {
        return response()->make(null, 200);
    }
}
