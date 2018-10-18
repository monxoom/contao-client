<?php
namespace Comolo\SuperLoginClient\ContaoEdition\Foundation\User;

use Comolo\SuperLoginClient\ContaoEdition\Exception\UserNotYetCreatedException;
use Doctrine\DBAL\Connection as DatabaseConnection;
use Comolo\SuperLoginClient\ContaoEdition\Exception\InvalidUserDetailsException;
use Comolo\SuperLoginClient\ContaoEdition\Foundation\Security\ContaoBackendLogin;
use Symfony\Component\Routing\RouterInterface;

class RemoteUserManager
{
    protected $connection;
    protected $router;
    protected $contaoBackendLogin;
    protected $remoteContaoOAuth2User;
    
    public function __construct(
        DatabaseConnection $connection,
        RouterInterface $router,
        ContaoBackendLogin $contaoBackendLogin,
        RemoteContaoOAuth2User $remoteContaoOAuth2User
    ) {
        $this->connection = $connection;
        $this->router = $router;
        $this->contaoBackendLogin = $contaoBackendLogin;
        $this->remoteContaoOAuth2User = $remoteContaoOAuth2User;
    }
    
    public function create(array $userData)
    {
        // Pass data to user object
        foreach($userData as $field => $value)
        {
            $this->remoteContaoOAuth2User->set($field, $value);
        }
        
        return $this->remoteContaoOAuth2User;
    }
    
    public function createOrUpdate(RemoteUserInterface $user)
    {
        if (!$user->validate()) {
            throw new InvalidUserDetailsException();
        }

        $userId = $this->connection->fetchColumn('SELECT id FROM tl_user WHERE username = ?', [$user->getUsername()]);

        if (!$userId) {
            // Create User
            $this->connection->insert('tl_user', $user->toArray());
            $userId = $this->connection->lastInsertId();
        }
        else {
            // Update User
            $this->connection->update('tl_user', $user->toArray(), ['id' => $userId]);
        }

        $user->setId($userId);
    }
    
    public function loginAs(RemoteUserInterface $user)
    {
        if (!$user->getId()) {
            throw new UserNotYetCreatedException();
        }

        $this->contaoBackendLogin->login($user);
    }
}
