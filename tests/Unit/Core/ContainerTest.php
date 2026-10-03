<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Core\Container;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
    public function testResolvesRegisteredClosure(): void
    {
        $container = new Container();
        $container->set('answer', static fn (): int => 42);

        self::assertSame(42, $container->get('answer'));
    }
}
