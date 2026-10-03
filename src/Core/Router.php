<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var list<array{method:string, path:string, handler:callable|array{class-string,string}, middleware:list<class-string>}> */
    private array $routes = [];

    /**
     * @param callable|array{class-string,string} $handler
     * @param list<class-string> $middleware
     */
    public function add(string $method, string $path, callable|array $handler, array $middleware = []): void
    {
        $this->routes[] = compact('method', 'path', 'handler', 'middleware');
    }

    /**
     * @param callable|array{class-string,string} $handler
     * @param list<class-string> $middleware
     */
    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    /**
     * @param callable|array{class-string,string} $handler
     * @param list<class-string> $middleware
     */
    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function dispatch(Request $request, Container $container): Response
    {
        foreach ($this->routes as $route) {
            $pattern = '#^' . preg_replace('/\{[a-zA-Z_]+\}/', '([^/]+)', $route['path']) . '$#';
            if ($route['method'] !== $request->method || !preg_match($pattern, $request->path, $matches)) {
                continue;
            }
            array_shift($matches);
            $destination = fn (Request $current): Response => $this->invoke($route['handler'], $current, $container, $matches);
            foreach (array_reverse($route['middleware']) as $middlewareClass) {
                $next = $destination;
                $middleware = $container->get($middlewareClass);
                $destination = fn (Request $current): Response => $middleware->handle($current, $next);
            }
            return $destination($request);
        }
        return new Response('Not Found', 404);
    }

    /**
     * @param callable|array{class-string,string} $handler
     * @param list<string> $parameters
     */
    private function invoke(callable|array $handler, Request $request, Container $container, array $parameters): Response
    {
        if (is_array($handler)) {
            $handler = [$container->get($handler[0]), $handler[1]];
        }
        return $handler($request, ...$parameters);
    }
}
