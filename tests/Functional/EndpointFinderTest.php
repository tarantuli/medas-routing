<?php

declare(strict_types=1);

namespace Medas\RoutingTest\Functional;

use Medas\Routing\EndpointFinder;
use Medas\RoutingTest\MockUps\Project;
use PHPUnit\Framework\TestCase;

class EndpointFinderTest extends TestCase
{
    public function testForInstance(): void
    {
        $route = service(EndpointFinder::class)->forEntity(new Project(1));
        self::assertEquals('/projects/1', $route);
    }

    public function testForCollection(): void
    {
        $route = service(EndpointFinder::class)->forCollection(Project::class);
        self::assertEquals('/projects', $route);
    }
}
