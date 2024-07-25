<?php

declare(strict_types=1);

namespace Medas\RoutingTest\Functional;

use Medas\Routing\{HandlerFinder, RouteHandler};
use PHPUnit\Framework\TestCase;

class HandlerFinderTest extends TestCase
{
    public function testBasic(): void
    {
        $routesFinder = service(HandlerFinder::class);
        $routes = $routesFinder->getAll();

        self::assertCount(5, $routes);
        self::assertInstanceOf(RouteHandler::class, $routes[0]);
    }
}
