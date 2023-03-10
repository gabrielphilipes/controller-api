<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
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
    }
    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $e
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        if ($request->wantsJson()) {
            match (true) {
                $e instanceof NotFoundHttpException => $e = new NotFoundException($e->getMessage()),
                $e instanceof ModelNotFoundException => $e = new \App\Exceptions\ModelNotFoundException($e->getMessage()),
                $e instanceof ThrottleRequestsException => $e = new TooManyAttempsException($e->getMessage()),
                $e instanceof MissingAbilityException => $e = new InsufficientPermissionsExpcetion($e->getMessage()),
                $e instanceof AuthenticationException => $e = new \App\Exceptions\AuthenticationException($e->getMessage()),
                default => true,
            };
        }

        return parent::render($request, $e);
    }
}
