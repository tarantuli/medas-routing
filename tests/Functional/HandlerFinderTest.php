<?php

declare(strict_types=1);

namespace Medas\RoutingTest\Functional;

use Medas\Routing\Handler;
use Medas\Routing\HandlerFinder;
use PHPUnit\Framework\TestCase;

class HandlerFinderTest extends TestCase
{
    public function testBasic(): void
    {
        $routesFinder = service(HandlerFinder::class);

        $routes = $routesFinder->find();
        self::assertCount(5, $routes);
        self::assertInstanceOf(Handler::class, $routes[0]);
    }
}
