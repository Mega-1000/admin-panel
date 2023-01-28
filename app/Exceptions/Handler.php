<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\OAuth2\Server\Exception\OAuthServerException;

class Handler extends ExceptionHandler
{
    const ERROR_ACCESS_DENIED = 9;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];
    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if ($exception instanceof OAuthServerException && $exception->getCode() == self::ERROR_ACCESS_DENIED) {
            return;
        }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $exception
     * @return Response|JsonResponse
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof SoapException && $exception->errorToReturn !== '') {
            return response()->json([
                'status' => 422,
                'message' => ['status' => 422,
                    'message' => [
                        $exception->errorToReturn,
                    ],
                ],
            ]);
        }

        return parent::render($request, $exception);
    }
}
