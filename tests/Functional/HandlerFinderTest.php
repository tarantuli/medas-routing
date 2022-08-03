<?php

declare(strict_types=1);

namespace Medas\RoutingTest\Functional;

use Medas\Routing\HandlerFinder;
use Medas\Routing\Handlers\RoutedHandler;
use Medas\RoutingTest\MockUps\DirectHandler;
use PHPUnit\Framework\TestCase;

class HandlerFinderTest extends TestCase
{
    public function testBasic(): void
    {
        $routesFinder = service(HandlerFinder::class);

        $routes = $routesFinder->find();
        self::assertCount(6, $routes);
        self::assertInstanceOf(RoutedHandler::class, $routes[0]);
    }

    public function testDirectHandler(): void
    {
        $routesFinder = service(HandlerFinder::class);

        $routes = $routesFinder->find();
        self::assertInstanceOf(DirectHandler::class, $routes[5]);
    }
}
