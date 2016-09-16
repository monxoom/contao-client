<?php
namespace Comolo\SuperLoginClient\ContaoEdition\Foundation\Server;

use Doctrine\DBAL\Connection as DatabaseConnection;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use League\OAuth2\Client\Provider\GenericProvider as OAuth2GenericProvider;

class ServerManager
{
    protected $connection;
    protected $router;

    public function __construct(DatabaseConnection $connection, Router $router)
    {
        $this->connection = $connection;
        $this->router = $router;
    }

    public function find($id)
    {
        $stmt = $this->connection->prepare('SELECT * FROM tl_superlogin_server WHERE id = :id LIMIT 1');
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) === 1) {
            return (object) $result[0];
        }

        return null;
    }

    public function generateReturnUrl($id)
    {
		return $this->router->generateUrl('superlogin_auth', ['serverId' => $id], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function createOAuth2Provider($server)
    {
        $provider = new OAuth2GenericProvider([
            'clientId'                => $server->public_id,
            'clientSecret'            => $server->secret,
            'redirectUri'             => $this->generateReturnUrl($server->id),
            'urlAuthorize'            => $server->url_authorize,
            'urlAccessToken'          => $server->url_access_token,
            'urlResourceOwnerDetails' => $server->url_resource_owner_details,
        ]);

        return $provider;
    }
}
