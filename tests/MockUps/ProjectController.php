<?php

declare(strict_types=1);

namespace Medas\RoutingTest\MockUps;

use Medas\Core\Attributes\Service;
use Medas\Routing\{Methods\Get, Parameters\Constant, Parameters\Integer, Route};

#[Service, Route(new Constant('projects'), Project::class)]
class ProjectController
{
    #[Get(isCollectionEndpoint: true)]
    public function getCollection(): array
    {
        return ['a', 'b'];
    }

    #[Get(new Integer('id'), isEntityEndpoint: true)]
    public function getEntity(int $id): Project
    {
        return new Project($id);
    }
}
