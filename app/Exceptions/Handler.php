<?php

namespace App\Exceptions;

use App\Http\Responses\Response;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {
        if ($e instanceof WeKastAPIException) {
            Log::error('[' . $e->getCode() . '] "' . $e->getMessage() . '" in '
                . (isset($e->getTrace()[0]['file'])
                    ? $e->getTrace()[0]['file'] . ':' . $e->getTrace()[0]['line']
                    : "nofile"));
        } else {
            parent::report($e);
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof WeKastNoFileException) {
            return Response::error($e->getCode(), $e->getMessage(), 404);
        } else if ($e instanceof WeKastAPIException) {
            return Response::error($e->getCode(), $e->getMessage());
        } else {
            return parent::render($request, $e);
        }
    }
}
