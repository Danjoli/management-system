<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Security\Csrf;

final readonly class CsrfMiddleware
{
    public function __construct(private Csrf $csrf)
    {
    }

    public function handle(Request $request, callable $next): Response
    {
        if ($request->method === 'POST' && !$this->csrf->verify($request->input('_token'))) {
            return new Response('CSRF token mismatch', 419);
        }
        return $next($request);
    }
}
