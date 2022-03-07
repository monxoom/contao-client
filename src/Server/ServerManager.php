<?php
namespace Comolo\SuperLoginClient\ContaoEdition\Server;

use Comolo\SuperLoginClient\ContaoEdition\Model\SuperLoginServerModel;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use League\OAuth2\Client\Provider\GenericProvider as OAuth2GenericProvider;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ServerManager
 * @package Comolo\SuperLoginClient\ContaoEdition\Server
 */
class ServerManager
{
    protected $connection;
    protected $router;
    protected $contaoFramework;

    /**
     * ServerManager constructor.
     * @param Connection $connection
     * @param RouterInterface $router
     * @param ContaoFrameworkInterface $contaoFramework
     */
    public function __construct(
        Connection $connection,
        RouterInterface $router,
        ContaoFrameworkInterface $contaoFramework
    ) {
        $this->connection = $connection;
        $this->router = $router;
        $this->contaoFramework = $contaoFramework;
    }

    /**
     * Find Login-Server
     * @param int $serverId
     * @return mixed
     */
    public function find(int $serverId)
    {
        if (!$this->contaoFramework->isInitialized()) $this->contaoFramework->initialize();

        return SuperLoginServerModel::findById($serverId);
    }

    /**
     * Generate return url
     * @param int $id
     * @return string
     */
    public function generateReturnUrl(int $id)
    {
		return $this->router->generate(
		    'superlogin_auth',
            ['serverId' => $id],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * Create OAuth2 Provider
     * @param SuperLoginServerModel $server
     * @return OAuth2GenericProvider
     */
    public function createOAuth2Provider(SuperLoginServerModel $server)
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
