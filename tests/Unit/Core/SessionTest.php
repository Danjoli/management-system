<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Core\Session;
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testStoresForgetsAndFlashesValues(): void
    {
        $session = new Session();
        $session->put('key', 'value');
        self::assertSame('value', $session->get('key'));
        $session->forget('key');
        self::assertNull($session->get('key'));
        $session->flash('status', 'saved');
        self::assertSame('saved', $session->pullFlash('status'));
        self::assertNull($session->pullFlash('status'));
    }
}
