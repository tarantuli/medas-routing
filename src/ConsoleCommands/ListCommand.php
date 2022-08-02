<?php

declare(strict_types=1);

namespace Medas\Routing\ConsoleCommands;

use Medas\ConfigOptions\Attributes\ConfigValue;
use Medas\Console\Commands\BaseConsoleCommand;
use Medas\Console\Commands\ConsoleCommandGroup;
use Medas\Console\Printer;
use Medas\Routing\ConfigOptions\GlobalPrefixOption;
use Medas\Routing\HandlerManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ListCommand extends BaseConsoleCommand
{
    public function __construct(
        private readonly HandlerManager $handlerManager,
        private readonly Printer        $printer,
        private readonly RoutingGroup   $group,

        #[ConfigValue(GlobalPrefixOption::class)]
        private readonly string|null    $globalPrefix,
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
                $handler->endpoint($this->globalPrefix),
                $handler->handlerName(),
                $handler->route()->name(),
            ]);
        }

        $this->printer->printTable($table);
    }
}
