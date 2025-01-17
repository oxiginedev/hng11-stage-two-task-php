<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/health',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies('*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (\Throwable $exception) {
            if ($exception instanceof BadRequestHttpException) {
                return response()->json([
                    'status' => 'Bad Request',
                    'message' => $exception->getMessage(),
                    'statusCode' => $exception->getStatusCode(),
                ], $exception->getStatusCode());
            }

            if ($exception instanceof ValidationException) {
                $errors = collect($exception->validator->errors()->all())
                    ->map(function ($field, $message) {
                        return [
                            'field' => $field,
                            'message' => $message[0]
                        ];
                    });

                return response()->json([
                    'errors' => $errors,
                ], 422);
            }

            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'status' => 'Bad Request',
                    'message' => 'Client error',
                    'statusCode' => 400,
                ], 400);
            }

            if ($exception instanceof HttpException) {
                return response()->json([
                    'status' => 'Bad Request',
                    'message' => $exception->getMessage(),
                    'statusCode' => $exception->getStatusCode(),
                ], $exception->getStatusCode());
            }

            Log::error($exception->getMessage(), ['exception' => $exception]);
        });
    })->create();
