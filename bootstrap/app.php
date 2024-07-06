<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/health',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies("*");
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (\Throwable $exception) {
            if ($exception instanceof HttpException) {
                return response()->json([
                    'status' => $exception->getMessage(),
                    'message' => $exception->getMessage(),
                    'statusCode' => $exception->getStatusCode(),
                ], $exception->getStatusCode());
            }

            if ($exception instanceof ValidationException) {
                return response()->json([
                    'errors' => $exception->validator->errors(),
                ], 422);
            }
        });
    })->create();
