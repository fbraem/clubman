<?php

namespace REST\Seasons\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Game\SeasonsTable;
use Domain\Game\SeasonTransformer;

class BrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        return (new \Core\ResourceResponse(
            SeasonTransformer::createForCollection(
                SeasonsTable::getTableFromRegistry()
                    ->find()
                    ->order(['start_date' => 'DESC'])
                    ->all()
            )
        ))($response);
    }
}
