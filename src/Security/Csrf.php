<?php

declare(strict_types=1);

namespace App\Security;

use App\Core\Session;

final readonly class Csrf
{
    public function __construct(private Session $session)
    {
    }

    public function token(): string
    {
        $token = $this->session->get('_csrf');
        if (!is_string($token)) {
            $token = bin2hex(random_bytes(32));
            $this->session->put('_csrf', $token);
        }
        return $token;
    }

    public function verify(?string $token): bool
    {
        $stored = $this->session->get('_csrf');
        return is_string($stored) && is_string($token) && hash_equals($stored, $token);
    }
}
