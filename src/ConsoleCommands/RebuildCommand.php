<?php

declare(strict_types=1);

namespace Medas\Routing\ConsoleCommands;

use Medas\Console\Commands\{BaseConsoleCommand, ConsoleCommandGroup};
use Medas\Console\Printer;
use Medas\Console\Text;
use Medas\Routing\HandlerManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class RebuildCommand extends BaseConsoleCommand
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

    public function process(array $arguments): void
    {
        $this->handlerManager->primeCache();
        $this->printer->print(Text::create('Rebuilt the cache!'));
    }
}
