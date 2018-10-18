<?php
namespace Comolo\SuperLoginClient\ContaoEdition\Security;


use Comolo\SuperLoginClient\ContaoEdition\User\RemoteUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Contao\CoreBundle\Security\User\ContaoUserProvider;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;

class ContaoBackendLogin
{
	protected static $loginGranted = false;

	protected $contaoUserProvider;
	protected $session;
	protected $tokenStorage;
	protected $authenticationManager;

    /**
     * ContaoBackendLogin constructor.
     * @param ContaoUserProvider $contaoUserProvider
     * @param SessionInterface $session
     * @param ContaoFrameworkInterface $contaoFramework
     * @param TokenStorageInterface $tokenStorage
     * @param AuthenticationManagerInterface $authenticationManager
     */
	public function __construct(
	    ContaoUserProvider $contaoUserProvider,
        SessionInterface $session,
        ContaoFrameworkInterface $contaoFramework,
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager

    ) {
        $this->contaoUserProvider = $contaoUserProvider;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;

        if (!$contaoFramework->isInitialized()) $contaoFramework->initialize();
    }

    /**
     * @param RemoteUserInterface $remoteUser
     */
    public function login(RemoteUserInterface $remoteUser)
    {
        /**
         * Will be deprecated in Contao 5!!
         * @deprecated contains deprecated code
         */
        $GLOBALS['TL_HOOKS']['checkCredentials'][] = [ContaoBackendLogin::class, 'hookCheckCredentials'];
		self::$loginGranted = true;

		$user = $this->contaoUserProvider->loadUserByUsername($remoteUser->getUsername());

		$firewallContext = 'contao_backend';

		$token = new UsernamePasswordToken(
			$user->username, 
			null,
			$firewallContext,
			$user->getRoles()
		);

		$authToken = $this->authenticationManager->authenticate($token);
        $this->tokenStorage->setToken($authToken);
		
		/*
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $event = new InteractiveLoginEvent($request, $token);
        $this->container->get('event_dispatcher')->dispatch('security.interactive_login', $event);
		*/
		
		$this->session->set('_security_'.$firewallContext, serialize($authToken));
        $this->session->save();
    }
	
	/**
	 * Overwrite contao password mechanism
     * @deprecated in Contao 5
	 */
	public function hookCheckCredentials()
	{
		return self::$loginGranted;
	}
}
