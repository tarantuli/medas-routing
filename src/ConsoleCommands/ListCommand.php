<?php

declare(strict_types=1);

namespace Medas\Routing\ConsoleCommands;

use Medas\Console\Commands\{BaseConsoleCommand, ConsoleCommandGroup};
use Medas\Console\Printer;
use Medas\Console\Table;
use Medas\Core\Attributes\Service;
use Medas\Routing\HandlerManager;
use Medas\Routing\Handlers\RoutedHandler;

#[Service]
readonly class ListCommand extends BaseConsoleCommand
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

    public function process(array $arguments): void
    {
        $this->printer->print();
        $table = Table::create(['Method', 'Endpoint', 'Handler', 'Name']);

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

        $this->printer
            ->printLine()
            ->print($table);
    }
}
