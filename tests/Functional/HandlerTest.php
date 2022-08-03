<?php

declare(strict_types=1);

namespace Medas\RoutingTest\Functional;

use Medas\Routing\HandlerManager;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    public function testExecuteHandler(): void
    {
        $handler = service(HandlerManager::class)->find('GET', '/projects');

        self::assertEquals(['a', 'b'], $handler->handle('GET', '/projects/'));
    }
}
