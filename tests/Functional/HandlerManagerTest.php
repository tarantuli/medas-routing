<?php

declare(strict_types=1);

namespace Medas\RoutingTest\Functional;

use Medas\Routing\HandlerManager;
use PHPUnit\Framework\TestCase;

class HandlerManagerTest extends TestCase
{
    public function testFindHandler(): void
    {
        $handler = service(HandlerManager::class)->find('get', '/projects');
        self::assertEquals(['a', 'b'], $handler->handle('/projects'));
    }

    public function testFindItemHandler(): void
    {
        $handler = service(HandlerManager::class)->find('get', '/projects/1');
        self::assertEquals(1, $handler->handle('/projects/1'));
    }

    public function testDontMatchText(): void
    {
        $handler = service(HandlerManager::class)->find('get', '/projects/name');
        self::assertNull($handler);
    }

    public function testFindAndExecute(): void
    {
        $result = service(HandlerManager::class)->findAndExecute('get', '/projects/1');
        self::assertEquals(1, $result);
    }

    public function testFindHighestPriority(): void
    {
        $handler = service(HandlerManager::class)->find('get', '/priority-entrypoint');
        self::assertEquals(10, $handler->priority());
    }
}
