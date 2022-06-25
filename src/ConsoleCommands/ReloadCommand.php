<?php

declare(strict_types=1);

namespace Medas\Routing\ConsoleCommands;

use Medas\Console\Commands\BaseConsoleCommand;
use Medas\Console\Commands\ConsoleCommandGroup;
use Medas\Console\Printer;
use Medas\Routing\HandlerManager;

class ReloadCommand extends BaseConsoleCommand
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
        return 'rebuild';
    }

    public function description(): string
    {
        return 'Clears the cache and rebuilds the route list';
    }

    public function process(array $arguments)
    {
        $this->handlerManager->primeCache();
        $this->printer->printLine(new Printer\Text('Rebuilt the cache!'));
    }
}
