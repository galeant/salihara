<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Route;

class Handler extends ExceptionHandler
{
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
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        if (
            $exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException ||
            $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
        ) {
            return response()->json([
                'code' => 500,
                'message' => 'Route Not Found',
                'result' => NULL
            ], 500);
        }

        $uri = Route::current()->uri;
        if (str_starts_with($uri, 'admin') || str_starts_with($uri, 'customer')) {

            $code = 500;
            $message = $exception->getMessage();
            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                $code = 422;
                $message = [];
                foreach ($exception->errors() as $ky => $err) {
                    $message[$ky] = $err[0];
                }
            }
            return response()->json([
                'code' => $code,
                'message' => $message,
                'result' => NULL
            ], $code);
        }
        return parent::render($request, $exception);
    }
}
