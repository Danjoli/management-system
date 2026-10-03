<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

final class Application
{
    public readonly Router $router;

    public function __construct(public readonly Container $container)
    {
        $this->router = new Router();
    }

    public function run(Request $request): Response
    {
        try {
            return $this->router->dispatch($request, $this->container);
        } catch (Throwable $exception) {
            $config = $this->container->get('config');
            $message = $config['debug'] ? $exception->getMessage() : 'Internal Server Error';
            return new Response($message, 500);
        }
    }
}
