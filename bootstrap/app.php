<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AgentMiddleware;
use App\Http\Middleware\CustomerAuth;
use App\Http\Middleware\EnsureApiActor;
use App\Http\Middleware\EnsureCommitteeMember;
use App\Http\Middleware\EnsureSurveyor;
use App\Http\Middleware\EnsurePortalUser;
use App\Http\Middleware\StaffMiddleware;
use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.customer' => CustomerAuth::class,
            'admin'         => AdminMiddleware::class,
            'staff'         => StaffMiddleware::class,
            'agent'         => AgentMiddleware::class,
            'surveyor'      => EnsureSurveyor::class,
            'committee'     => EnsureCommitteeMember::class,
            'api.actor'     => EnsureApiActor::class,
            'portal.user'   => EnsurePortalUser::class
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/client-errors',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request, Throwable $e) => $request->is('api/*') || $request->expectsJson()
        );

        // One JSON error shape for everything under /api
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            if ($e instanceof ValidationException) {
                return ApiResponse::error('validation_failed', 'The given data was invalid.', 422, $e->errors());
            }

            if ($e instanceof AuthenticationException) {
                return ApiResponse::error('unauthenticated', 'Missing, invalid or expired token.', 401);
            }

            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                $code   = match ($status) {
                    403 => 'forbidden',
                    404 => 'not_found',
                    405 => 'method_not_allowed',
                    429 => 'too_many_requests',
                    default => 'http_error',
                };
                $message = $status === 404 ? 'Resource not found.' : ($e->getMessage() ?: 'Request failed.');

                return ApiResponse::error($code, $message, $status, null, $e->getHeaders());
            }

            return ApiResponse::error(
                'server_error',
                config('app.debug') ? $e->getMessage() : 'Something went wrong.',
                500
            );
        });
    })
    ->create();
