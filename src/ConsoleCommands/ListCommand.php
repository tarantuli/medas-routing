<?php

declare(strict_types=1);

namespace Medas\Routing\ConsoleCommands;

use Medas\Console\Commands\BaseConsoleCommand;
use Medas\Console\Commands\ConsoleCommandGroup;
use Medas\Console\Printer;
use Medas\Routing\HandlerManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ListCommand extends BaseConsoleCommand
{
    public function __construct(
        private HandlerManager $handlerManager,
        private Printer        $printer,
        private RoutingGroup   $group,
    )
    {
    }

    public function group(): ConsoleCommandGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'list';
    }

    public function description(): string
    {
        return 'Prints a list of routes';
    }

    public function process(array $arguments)
    {
        $table = new Printer\Table(['method', 'endpoint', 'handler']);

        foreach ($this->handlerManager->getActualHandlers() as $handler) {
            $table->addData([
                $handler->method()->name(),
                $handler->endpoint(),
                $handler->handlerName(),
            ]);
        }

        $this->printer->printTable($table);
    }
}
