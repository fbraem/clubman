<?php

namespace REST\Auth\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use League\OAuth2\Server\Exception\OAuthServerException;

use Core\Responders\Responder;
use Core\Responders\HTTPCodeResponder;

class LogoutAction implements \Core\ActionInterface
{
    use \League\Oauth2\Server\CryptTrait;

    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $db = $request->getAttribute('clubman.container')['db'];
        $config = $request->getAttribute('clubman.config');
        $this->setEncryptionKey($config->oauth2->encryption_key);
        $server = $request->getAttribute('clubman.container')['authorizationServer'];

        $refreshTokenTable = new \Domain\Auth\RefreshTokenTable($db);
        $token = json_decode($this->decrypt($request->getParsedBody()['refresh_token']));
        $refreshTokenTable->revokeRefreshToken($token->refresh_token_id);

        $accessTokenTable = new \Domain\Auth\AccessTokenTable($db);
        $accessTokenTable->revokeAccessToken($token->access_token_id);

        return (new HTTPCodeResponder(new Responder(), 200))->respond();
    }
}
