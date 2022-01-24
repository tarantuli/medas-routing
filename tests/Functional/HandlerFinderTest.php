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

        self::assertInstanceOf(Handler::class, $routesFinder->find()[0]);
    }
}
