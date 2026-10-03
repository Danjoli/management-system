<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Core\Session;
use App\Security\Csrf;
use PHPUnit\Framework\TestCase;

final class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testGeneratesAndVerifiesSessionToken(): void
    {
        $csrf = new Csrf(new Session());
        $token = $csrf->token();

        self::assertSame(64, strlen($token));
        self::assertTrue($csrf->verify($token));
        self::assertFalse($csrf->verify('invalid'));
    }
}
