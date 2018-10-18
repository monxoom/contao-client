<?php
namespace Comolo\SuperLoginClient\ContaoEdition\Foundation\Server;

use Comolo\SuperLoginClient\ContaoEdition\Model\SuperLoginServerModel;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use League\OAuth2\Client\Provider\GenericProvider as OAuth2GenericProvider;
use Symfony\Component\Routing\RouterInterface;

class ServerManager
{
    protected $connection;
    protected $router;

    public function __construct(Connection $connection, RouterInterface $router)
    {
        $this->connection = $connection;
        $this->router = $router;
    }

    public function find(int $id)
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

    public function generateReturnUrl(int $id)
    {
		return $this->router->generate(
		    'superlogin_auth',
            ['serverId' => $id],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * TODO: model instead of object
     * @param object $server
     * @return OAuth2GenericProvider
     */
    public function createOAuth2Provider(object $server)
    {
        return new OAuth2GenericProvider([
            'clientId'                => $server->public_id,
            'clientSecret'            => $server->secret,
            'redirectUri'             => $this->generateReturnUrl($server->id),
            'urlAuthorize'            => $server->url_authorize,
            'urlAccessToken'          => $server->url_access_token,
            'urlResourceOwnerDetails' => $server->url_resource_owner_details,
        ]);
    }
}
