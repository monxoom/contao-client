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
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\BrowserKit\Cookie;

class ContaoBackendLogin
{
    protected $container;
	protected static $loginGranted = false;

	public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function login(RemoteUserInterface $remoteUser)
    {
		if (!$this->container) {
			throw new \Exception('Container missing');
		}
		
		$this->container->get('contao.framework')->initialize();
		
        // Add contao hook
		// Will be deprecated in Contao 5!!
        $GLOBALS['TL_HOOKS']['checkCredentials'][] = [
            'Comolo\SuperLoginClient\ContaoEdition\Foundation\Security\ContaoBackendLogin',
            'hookCheckCredentials'
        ];
		self::$loginGranted = true;
		

		$user = $this->container->get('contao.security.backend_user_provider')->loadUserByUsername($remoteUser->getUsername());
		$session = $this->container->get('session');

		$firewallContext = 'contao_backend';


		$token = new UsernamePasswordToken(
			$user->username, 
			null,
			$firewallContext,
			$user->getRoles()
		);

		$authToken = $this->container->get('security.authentication.manager')->authenticate($token);
		$this->container->get('security.token_storage')->setToken($authToken); // $authToken
		
		/*
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $event = new InteractiveLoginEvent($request, $token);
        $this->container->get('event_dispatcher')->dispatch('security.interactive_login', $event);
		*/
		
		$session->set('_security_'.$firewallContext, serialize($authToken));
		$session->save();
    }
	
	/**
	 * Overwrite contao password mechanism
	 */
	public function hookCheckCredentials()
	{
		return self::$loginGranted;
	}
}
