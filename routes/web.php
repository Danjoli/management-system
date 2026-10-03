<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Response;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\CsrfMiddleware;

return static function (Application $app): void {
    $app->router->get('/', static fn (): Response => Response::redirect('/dashboard'));
    $app->router->get('/login', [AuthController::class, 'form']);
    $app->router->post('/login', [AuthController::class, 'login'], [CsrfMiddleware::class]);
    $app->router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class, CsrfMiddleware::class]);
    $app->router->get('/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class]);
    $app->router->get('/manage/{resource}', [ManagementController::class, 'index'], [AuthMiddleware::class]);
    $app->router->post('/manage/{resource}', [ManagementController::class, 'store'], [AuthMiddleware::class, CsrfMiddleware::class]);
    $app->router->get('/reports', [ReportController::class, 'index'], [AuthMiddleware::class]);
    $app->router->get('/reports/export', [ReportController::class, 'csv'], [AuthMiddleware::class]);
    $app->router->get('/health', static fn (): Response => Response::json([
        'status' => 'ok',
        'service' => 'sistema-gestao-php',
    ]));
};
