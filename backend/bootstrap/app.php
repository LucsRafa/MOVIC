<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                Log::warning('API validation error', [
                    'path' => $request->path(),
                    'user_id' => $request->user()?->id,
                    'errors' => $e->errors(),
                    'has_files' => $request->files->count() > 0,
                    'content_length' => $request->server('CONTENT_LENGTH'),
                ]);
                return response()->json([
                    'status' => 'error',
                    'error' => 'validation_error',
                    'message' => 'Dados inválidos.',
                    'details' => $e->errors(),
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'unauthenticated',
                    'message' => 'Usuário não autenticado. Envie o token.',
                ], 401);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'forbidden',
                    'message' => 'Você não tem permissão para esta ação.',
                ], 403);
            }
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'not_found',
                    'message' => 'Recurso não encontrado.',
                ], 404);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'route_not_found',
                    'message' => 'Rota não encontrada.',
                ], 404);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'method_not_allowed',
                    'message' => 'Método HTTP não permitido para esta rota.',
                ], 405);
            }
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                Log::error('API exception', [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                ]);

                $response = [
                    'status' => 'error',
                    'error' => 'server_error',
                    'message' => 'Erro interno no servidor.',
                ];

                if (config('app.debug')) {
                    $response['debug'] = [
                        'exception' => get_class($e),
                        'message' => $e->getMessage(),
                    ];
                }

                return response()->json($response, 500);
            }
        });
    })->create();
