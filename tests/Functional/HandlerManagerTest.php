<?php

declare(strict_types=1);

namespace Medas\RoutingTest\Functional;

use Medas\Routing\Handler;
use Medas\Routing\HandlerManager;
use PHPUnit\Framework\TestCase;

class HandlerManagerTest extends TestCase
{
    public function testFindHandler(): void
    {
        $handler = service(HandlerManager::class)->find('get', '/projects');
        self::assertInstanceOf(Handler::class, $handler);
    }

    public function testFindItemHandler(): void
    {
        $handler = service(HandlerManager::class)->find('get', '/projects/1');
        self::assertInstanceOf(Handler::class, $handler);
    }

    public function testDontMatchText(): void
    {
        $handler = service(HandlerManager::class)->find('get', '/projects/name');
        self::assertNull($handler);
    }
}
