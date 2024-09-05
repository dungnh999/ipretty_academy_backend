<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Exceptions\BaseException;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render($request, Throwable $e)
    {
        $statusCode = 400;
        $errors = [];
        $message = $e->getMessage(); //__('messages.errors.unexpected');
        $messageCode = '';
        switch (true) {
            case $e instanceof ValidationException:
                $message = 'sdsdsđsd';
                $errors = $e->errors();
                $statusCode = 2222;
                break;

            case $e instanceof NotFoundHttpException:
            case $e instanceof AuthorizationException:
                $message = __('messages.errors.route');
                $statusCode = 404;
                $messageCode = 'route.not_found';
                break;
            case $e instanceof MethodNotAllowedHttpException:
            case $e instanceof AccessDeniedHttpException:
                $message = __('messages.errors.route');
                $statusCode = 403;
                $messageCode = 'route.access_denied';
                break;

            case $e instanceof ModelNotFoundException:
                $message = $e->getMessage(); //__('messages.errors.data');
                $statusCode = 404;
                $messageCode = 'record.not_found';
                break;

            case $e instanceof AuthenticationException:
                $message = __('messages.errors.unauthorized');
                $statusCode = 401;
                $messageCode = 'session.not_found';
                break;

            case $e instanceof ThrottleRequestsException:
                $message = __('messages.errors.many_attempts');
                $messageCode = 'request.max_attemps';
                break;

            case $e instanceof BaseException:
                $message = $e->getMessage();
                $messageCode = method_exists($e, 'getMessageCode') ? $e->getMessageCode() : null;
                $statusCode = $e->getCode();
                break;

            default:
                break;
        }

        $data = [
            'success' => false,
            'message' => $message,
            'code' => $messageCode,
            'status_code' => $statusCode
        ];

        if (!empty($errors)) {
            $data['errors'] = $errors;
        }


        if ($request->is('api/*')) {
            return response()->json($data, $statusCode);
        }
        return parent::render($request, $e);
    }

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });
    }
}
