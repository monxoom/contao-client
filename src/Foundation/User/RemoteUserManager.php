<?php
namespace Comolo\SuperLoginClient\ContaoEdition\Foundation\User;

use Doctrine\DBAL\Connection as DatabaseConnection;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Comolo\SuperLoginClient\ContaoEdition\Exception\InvalidUserDetailsException;
use Comolo\SuperLoginClient\ContaoEdition\Foundation\Security\ContaoBackendLogin;
use Contao\CoreBundle\Security\Authentication\ContaoToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Contao\BackendUser as ContaoBackendUser;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class RemoteUserManager
{ 
    protected $connection;
    protected $router;
    protected $container;
    
    public function __construct(Container $container, DatabaseConnection $connection, Router $router)
    {
        $this->connection = $connection;
        $this->router = $router;
        $this->container = $container;
    }
    
    public function create($userData)
    {
        // Validate user data
        if (!is_array($userData)) {
            throw new InvalidUserDetailsException();
        }
        
        // Create user object
        $remoteUser = new RemoteContaoOAuth2User();
        $remoteUser->setDatabaseConnection($this->connection);
        
        // Pass data to user object
        foreach($userData as $field => $value)
        {
            $remoteUser->set($field, $value);
        }
        
        return $remoteUser;
    }
    
    public function createOrUpdate(RemoteUserInterface $user)
    {
        if (!$user->validate()) {
            throw new InvalidUserDetailsException();
        }
        
        // Save user to database
        $userId = $this->connection->fetchColumn('SELECT id FROM tl_user WHERE username = ?', array($user->getUsername()));
        
        // Create User
        if (!$userId) {
            $this->connection->insert('tl_user', $user->toArray());
            $userId = $this->connection->lastInsertId();
        }
        
        // Update User
        else {
            $this->connection->update('tl_user', $user->toArray(), array('id' => $userId));
        }
        
        
        $user->setId($userId);
    }
    
    public function loginAs(RemoteUserInterface $user)
    {	
        // Check if user already exists in the database
        if (!$user->getId()) {
            throw new UserNotCreatedYetException();
        }

        $login = $this->container->get(ContaoBackendLogin::class);

        return $login->login($user);
    }
}
