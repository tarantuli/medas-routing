<?php

declare(strict_types=1);

namespace Medas\RoutingTest\Functional;

use Medas\Routing\HandlerManager;
use Medas\RoutingTest\MockUps\Project;
use PHPUnit\Framework\TestCase;

class HandlerManagerTest extends TestCase
{
    public function testFindHandler(): void
    {
        $handler = service(HandlerManager::class)->find('GET', '/projects/1');
        self::assertInstanceOf(Project::class, $handler->handle('/projects/1'));
    }

    public function testDontMatchText(): void
    {
        $handler = service(HandlerManager::class)->find('GET', '/projects/name');
        self::assertNull($handler);
    }

    public function testFindAndExecute(): void
    {
        $result = service(HandlerManager::class)->findAndExecute('GET', '/projects/1');
        self::assertInstanceOf(Project::class, $result);
    }

    public function testFindHighestPriority(): void
    {
        $handler = service(HandlerManager::class)->find('GET', '/priority-entrypoint');
        self::assertEquals(10, $handler->priority());
    }
}
