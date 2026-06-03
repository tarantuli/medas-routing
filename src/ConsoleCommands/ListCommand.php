<?php

declare(strict_types=1);

namespace Medas\Routing\ConsoleCommands;

use Medas\Console\{
    Commands\BaseConsoleCommand,
    Commands\CommandInput,
    Commands\ConsoleCommandGroup,
    Commands\Option,
    Formats\SafeColor,
    Printer,
    Table,
    Text
};
use Medas\Core\Attributes\Service;
use Medas\Routing\{HandlerManager, RouteHandler};

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

    public function aliases(): array
    {
        return ['routes'];
    }

    public function maxArgumentCount(): int
    {
        return 1;
    }

    public function options(): array
    {
        return [
            Option::valueRequired('method', 'm'),
        ];
    }

    public function process(CommandInput $input): void
    {
        $this->printer->print();

        $table = Table::create(['Method', 'Endpoint', 'Handler', 'Name']);

        foreach ($this->handlerManager->getHandlers() as $handler) {
            if ($handler instanceof RouteHandler) {
                $routeName = $handler->method()->routeName();
                $methodReflector = $handler->handlerMethod();
                $handlerName = "$methodReflector->class::$methodReflector->name";

                if ($input->hasOption('method')
                        && !strcasecmp($input->getOption('method'), $handler->method()->name())) {
                    continue;
                }

                if ($input->hasArgument(0) && !strcasecmp($input->getArgument(0), $handler->endpointName())) {
                    continue;
                }

                $table->data[] = [
                    Text::create($handler->method()->name(), SafeColor::LightGray),
                    Text::create($handler->endpointPattern(), SafeColor::Green),
                    Text::create($handlerName, SafeColor::LightGray),
                    $routeName !== null ? Text::create($routeName, SafeColor::LightGray) : null,
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
            ->print($table)
            ->printLine();
    }
}
