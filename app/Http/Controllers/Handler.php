<?php

namespace App\Http\Controllers;

use App\Exceptions\ConnectionException;
use App\Exceptions\HttpException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $exception)
    {
//        if ($exception instanceof QueryException || $exception instanceof ConnectionException) {
//          return response()->view('contents.page.500', [], 500);
//        }
//
//        if ($exception instanceof HttpException) {
//          return $this->renderHttpException($exception);
//        }

        return parent::render($request, $exception);
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
