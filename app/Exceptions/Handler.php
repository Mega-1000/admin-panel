<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as MainHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends MainHandler
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
     * @param Throwable $e
     * @throws Throwable
     */
    public function report(Throwable $e): void
    {
        if ($e->getCode() == self::ERROR_ACCESS_DENIED) {
            return;
        }
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse|\Illuminate\Http\Response|Response
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof SoapException && $e->errorToReturn !== '') {
            return response()->json([
                'status' => 422,
                'message' => ['status' => 422,
                    'message' => [
                        $e->errorToReturn,
                    ],
                ],
            ]);
        }

        return parent::render($request, $e);
    }
}
