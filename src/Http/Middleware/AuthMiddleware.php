<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Security\Auth;

final readonly class AuthMiddleware
{
    public function __construct(private Auth $auth)
    {
    }

    public function handle(Request $request, callable $next): Response
    {
        return $this->auth->check() ? $next($request) : Response::redirect('/login');
    }
}
