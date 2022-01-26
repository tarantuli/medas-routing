<?php

declare(strict_types=1);

namespace Medas\RoutingTest\Functional\ConsoleCommands;

use Medas\Routing\ConsoleCommands\ListCommand;
use PHPUnit\Framework\TestCase;

class ListCommandTest extends TestCase
{
    public function testBasicUsage(): void
    {
        service(ListCommand::class)->process([]);
    }
}
