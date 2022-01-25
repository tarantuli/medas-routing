<?php

declare(strict_types=1);

namespace Medas\RoutingTest\MockUps;

use Medas\Routing\Methods\Get;
use Medas\Routing\Parameters\Constant;
use Medas\Routing\Route;
use Medas\ServiceManager\Attributes\Service;

#[Service, Route(new Constant('priority-entrypoint'))]
class PriorityController
{
    #[Get]
    public function priorityDefault()
    {
    }

    #[Get, Route\Priority(10)]
    public function priority10()
    {
    }

    #[Get, Route\Priority(1)]
    public function priority1()
    {
    }
}
