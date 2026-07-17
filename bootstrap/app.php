<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'admin.password' => \App\Http\Middleware\EnsureAdminPasswordConfirmed::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $e) {
            try {
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
                    && in_array($e->getStatusCode(), [404, 419, 422, 403], true)) {
                    return;
                }

                \App\Services\SystemLogService::error('app_error', $e->getMessage(), [
                    'exception' => get_class($e),
                    'file'      => $e->getFile() . ':' . $e->getLine(),
                    'url'       => request()?->fullUrl(),
                ]);
            } catch (\Throwable) {
                // 로그 기록 실패는 조용히 무시
            }
        });
    })->create();
