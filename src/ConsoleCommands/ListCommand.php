<?php

declare(strict_types=1);

namespace Medas\Routing\ConsoleCommands;

use Medas\Console\{
    Commands\BaseConsoleCommand,
    Commands\ConsoleCommandGroup,
    Formats\Color,
    Printer,
    Table,
    Text
};
use Medas\Core\Attributes\Service;
use Medas\Routing\{HandlerManager, Handlers\RoutedHandler};

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
                $routeName = $handler->method()->routeName();

                $table->data[] = [
                    Text::create($handler->method()->name(), Color::LightGray),
                    Text::create($handler->endpointPattern(), Color::Green),
                    Text::create($handler->handlerName(), Color::LightGray),
                    $routeName !== null ? Text::create($routeName, Color::LightGray) : null,
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
