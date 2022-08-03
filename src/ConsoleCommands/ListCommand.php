<?php

declare(strict_types=1);

namespace Medas\Routing\ConsoleCommands;

use Medas\Console\Commands\{BaseConsoleCommand, ConsoleCommandGroup};
use Medas\Console\Printer;
use Medas\Routing\HandlerManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ListCommand extends BaseConsoleCommand
{
    public function __construct(
        private readonly HandlerManager $handlerManager,
        private readonly Printer        $printer,
        private readonly RoutingGroup   $group,
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
        $this->printer->printLine();
        $table = new Printer\Table(['method', 'endpoint', 'handler', 'name']);

        foreach ($this->handlerManager->getActualHandlers() as $handler) {
            $table->addData([
                $handler->method()->name(),
                $handler->endpointPattern(),
                $handler->handlerName(),
                $handler->method()->routeName(),
            ]);
        }

        $this->printer->printTable($table);
    }
}
