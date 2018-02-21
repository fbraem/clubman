<?php

namespace REST\Countries\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

use League\Fractal;

class BrowseAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $db = $request->getAttribute('clubman.container')['db'];
        $countries = (new \Domain\Person\CountriesTable($db))->find();
        $payload->setOutput(new Fractal\Resource\Collection($countries, new \Domain\Person\CountryTransformer(), 'countries'));

        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}