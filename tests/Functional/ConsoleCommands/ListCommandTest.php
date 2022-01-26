<?php

declare(strict_types=1);

namespace Medas\RoutingTest\Functional\ConsoleCommands;

use Medas\Routing\ConsoleCommands\ListCommand;
use PHPUnit\Framework\TestCase;

class ListCommandTest extends TestCase
{
    public function testBasicUsage(): void
    {
        ob_start();
        service(ListCommand::class)->process([]);
        $output = ob_get_clean();

        self::assertStringContainsString('/projects/:id', $output);
        self::assertStringContainsString('Medas\RoutingTest\MockUps\ProjectController::getItem', $output);
    }
}
