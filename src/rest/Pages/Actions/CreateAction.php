<?php

namespace REST\Pages\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Page\PageTransformer;
use Domain\Page\PagesTable;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;

class CreateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        try {
            (new InputValidator([
                'data.attributes.priority' => v::digit(),
                'data.attributes.enabled' => v::boolType()
            ]))->validate($data);

            $pagesTable = PagesTable::getTableFromRegistry();

            $category = (new EntityExistValidator('data.relationships.category', $pagesTable->Category, true))->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            $page = $pagesTable->newEntity();
            $page->category = $category;
            $page->remark = $attributes['remark'];
            $page->enabled = $attributes['enabled'];
            $page->priority = $attributes['priority'];
            $pagesTable->save($page);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $page->id);
            }

            $filesystem = $this->container->get('filesystem');
            $response = (
                new ResourceResponse(
                    PageTransformer::createForItem($page, $filesystem)
                )
            )($response)->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        }

        return $response;
    }
}
