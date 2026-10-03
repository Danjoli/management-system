<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Core\Request;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    public function testBodyTakesPrecedenceOverQueryInput(): void
    {
        $request = new Request('POST', '/', ['name' => 'query'], ['name' => 'body']);
        self::assertSame('body', $request->input('name'));
        self::assertSame('fallback', $request->input('missing', 'fallback'));
    }
}
