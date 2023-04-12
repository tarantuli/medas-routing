<?php

declare(strict_types=1);

namespace Medas\Routing\ConsoleCommands;

use Medas\Console\Commands\{BaseConsoleCommand, ConsoleCommandGroup};
use Medas\Console\Printer;
use Medas\Console\Table;
use Medas\Routing\HandlerManager;
use Medas\Routing\Handlers\RoutedHandler;
use Medas\ServiceManager\Service;

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

    public function process(array $arguments): void
    {
        $this->printer->print();
        $table = Table::create(['method', 'endpoint', 'handler', 'name']);

        foreach ($this->handlerManager->getActualHandlers() as $handler) {
            if ($handler instanceof RoutedHandler) {
                $table->data[] = [
                    $handler->method()->name(),
                    $handler->endpointPattern(),
                    $handler->handlerName(),
                    $handler->method()->routeName(),
                ];
            }
            else {
                $table->data[] = [
                    null,
                    $handler->endpointName(),
                    $handler::class . '::handle',
                    null,
                ];
            }
        }

        $this->printer->print($table);
    }
}
