<?php

declare(strict_types=1);

namespace Medas\RoutingTest\MockUps;

use Medas\Routing\Methods\Get;
use Medas\Routing\Parameters\Constant;
use Medas\Routing\Parameters\Integer;
use Medas\Routing\Route;
use Medas\ServiceManager\Attributes\Service;

#[Service, Route(new Constant('projects'))]
class ProjectController
{
    #[Get]
    public function getCollection(): array
    {
        return ['a', 'b'];
    }

    #[Get(new Integer('id'))]
    public function getItem(int $id): Project
    {
        return new Project($id);
    }
}
