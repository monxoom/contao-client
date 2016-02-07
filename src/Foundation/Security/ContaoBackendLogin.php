<?php
namespace Comolo\SuperLoginClient\ContaoEdition\Foundation\Security;

use Doctrine\DBAL\Connection as DatabaseConnection;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Comolo\SuperLoginClient\ContaoEdition\Exception\InvalidUserDetailsException;
use Contao\CoreBundle\Security\Authentication\ContaoToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Contao\BackendUser as ContaoBackendUser;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Comolo\SuperLoginClient\ContaoEdition\Foundation\User\RemoteUserInterface;

class ContaoBackendLogin
{
    protected $remoteUser;
    protected $container;
    static protected $grantLogin = false;
    static protected $grantLoginUsername = '';
    
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
    
    public function setRemoteUser(RemoteUserInterface $remoteUser)
    {
        $this->remoteUser = $remoteUser;
    }
      
    public function login()
    {
        $this->container->get('contao.framework')->initialize();
        $user = ContaoBackendUser::getInstance();
        
        // Prepare for login
        $_POST['username'] = $this->remoteUser->getUsername();
        $_POST['password'] = '####';
        
        // Activate password hook 
        self::$grantLogin = true;
        self::$grantLoginUsername = $this->remoteUser->getUsername();
        
        // Add contao hook
        $GLOBALS['TL_HOOKS']['checkCredentials'][] = [
            'Comolo\SuperLoginClient\ContaoEdition\Foundation\Security\ContaoBackendLogin', 
            'hookCheckCredentials'
        ];
        
        return $user->login();
        
        /*    
        // Create token
        $token = new ContaoToken($contaoUser);
        $this->get('security.context')->setToken($token);
        
        $request = $this->get('request');
        $event = new InteractiveLoginEvent($request, $token);
        $this->get('event_dispatcher')->dispatch('security.interactive_login', $event);
        */
    }
    
    public function hookCheckCredentials($username, $password)
    {
        return (self::$grantLogin && self::$grantLoginUsername == $username);
    }
}