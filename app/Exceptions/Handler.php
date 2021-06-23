<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{

    use ApiResponser;
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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        $this->renderable(function (Exception $e, $request) {
        });
    }
    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            $error_message = $e->validator->errors()->getMessages();
            return $this->errorResponse($error_message, 422);
        }
        if ($e instanceof ModelNotFoundException) {
            $model_name =  Str::lower(class_basename($e->getModel()));
            return $this->errorResponse('You are trying to get ' . $model_name . ' that does not exist.', 404);
        }
        if ($e instanceof AuthenticationException) {
            return $this->errorResponse('Unauthenticated', 401);
        }
        if ($e instanceof AuthorizationException) {
            return $this->errorResponse($e->getMessage(), 401);
        }
        if ($e instanceof NotFoundHttpException) {
            return $this->errorResponse('The requested URL could not be retrieved.', 404);
        }
        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse('The specified method for the request is invalid .', 404);
        }
        if ($e instanceof HttpException) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }
        // if (config('app.debug')) {
        //     return $this->errorResponse('Unexpected Exception', 500);
        // }
        return parent::render($request, $e);
    }
}
