<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Core\Container;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testDispatchesARegisteredRoute(): void
    {
        $router = new Router();
        $router->get('/hello/{name}', static fn (Request $request, string $name): Response => new Response("Hello {$name}"));

        $response = $router->dispatch(new Request('GET', '/hello/Danilo'), new Container());

        self::assertSame(200, $response->status);
        self::assertSame('Hello Danilo', $response->content);
    }

    public function testReturnsNotFoundForUnknownRoute(): void
    {
        $response = (new Router())->dispatch(new Request('GET', '/missing'), new Container());

        self::assertSame(404, $response->status);
    }
}
