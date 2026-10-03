<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Response;

return static function (Application $app): void {
    $app->router->get('/health', static fn (): Response => Response::json([
        'status' => 'ok',
        'service' => 'sistema-gestao-php',
    ]));
};
