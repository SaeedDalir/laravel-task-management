<?php

use App\Exceptions\MessageException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\UserIsAdmin::class,
            'role' => \App\Http\Middleware\UserHasRole::class,
            'permission' => \App\Http\Middleware\UserHasPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReport([
            MessageException::class,
        ]);

        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
        ]);

        $exceptions->renderable(function (MessageException $e, Request $request) {
            $code = $e->getCode() ?: ResponseCode::HTTP_UNPROCESSABLE_ENTITY;
            $message = $e->getMessage() ?: 'Something went wrong.';

            $arrayMessage = json_decode($message);
            if ($arrayMessage && isset($arrayMessage->message)) {
                return Response::error(
                    message: trim($arrayMessage->message),
                    data: ['body' => $arrayMessage->body ?? []],
                    code: $code,
                );
            }

            return Response::error(
                message: $message,
                code: $code,
            );
        });

        $exceptions->renderable(function (ValidationException $e, Request $request) {
            return Response::error(
                message: 'Validation failed.',
                data: $e->errors(),
                code: $e->status,
            );
        });

        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            return Response::error(
                message: 'Unauthenticated.',
                code: ResponseCode::HTTP_UNAUTHORIZED,
            );
        });

        $exceptions->renderable(function (AuthorizationException $e, Request $request) {
            return Response::error(
                message: 'Unauthorized.',
                code: ResponseCode::HTTP_FORBIDDEN,
            );
        });

        $exceptions->renderable(function (ModelNotFoundException $e, Request $request) {
            return Response::error(
                message: $e->getMessage(),
                code: ResponseCode::HTTP_BAD_REQUEST,
            );
        });
    })->create();
