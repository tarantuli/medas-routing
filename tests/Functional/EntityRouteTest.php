<?php

declare(strict_types=1);

namespace Medas\RoutingTest\Functional;

use Medas\Routing\EntityRoute;
use Medas\RoutingTest\MockUps\Project;
use PHPUnit\Framework\TestCase;

class EntityRouteTest extends TestCase
{
    public function testNamespacedClass(): void
    {
        $route = new EntityRoute(Project::class);

        self::assertEquals(Project::class, $route->endpointForEntity());
        self::assertEquals('project', $route->parameters()[0]->readablePattern());
    }

    public function testGlobalSpaceClass(): void
    {
        $route = new EntityRoute(\Exception::class);

        self::assertEquals(\Exception::class, $route->endpointForEntity());
        self::assertEquals('exception', $route->parameters()[0]->readablePattern());
    }
}
