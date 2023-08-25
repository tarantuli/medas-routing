<?php

declare(strict_types=1);

namespace Medas\Routing\ConsoleCommands;

use Medas\Console\Commands\{BaseConsoleCommand, ConsoleCommandGroup};
use Medas\Console\Printer;
use Medas\Console\Text;
use Medas\Core\Attributes\Service;
use Medas\Routing\HandlerManager;

#[Service]
readonly class RebuildCommand extends BaseConsoleCommand
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
