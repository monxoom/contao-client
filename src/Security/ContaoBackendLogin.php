<?php
namespace Comolo\SuperLoginClient\ContaoEdition\Security;


use Comolo\SuperLoginClient\ContaoEdition\User\RemoteUserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Contao\CoreBundle\Security\User\ContaoUserProvider;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class ContaoBackendLogin
{
	protected static $loginGranted = false;

	protected $contaoUserProvider;
	protected $session;
	protected $tokenStorage;
	protected $authenticationManager;
	protected $eventDispatcher;
	protected $requestStack;

    /**
     * ContaoBackendLogin constructor.
     * @param ContaoUserProvider $contaoUserProvider
     * @param SessionInterface $session
     * @param ContaoFrameworkInterface $contaoFramework
     * @param TokenStorageInterface $tokenStorage
     * @param AuthenticationManagerInterface $authenticationManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param RequestStack $requestStack
     */
	public function __construct(
	    ContaoUserProvider $contaoUserProvider,
        SessionInterface $session,
        ContaoFrameworkInterface $contaoFramework,
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack

    ) {
        $this->contaoUserProvider = $contaoUserProvider;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;

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
        //$GLOBALS['TL_HOOKS']['checkCredentials'][] = [ContaoBackendLogin::class, 'hookCheckCredentials'];
		//self::$loginGranted = true;

		$user = $this->contaoUserProvider->loadUserByUsername($remoteUser->getUsername());

		$firewallContext = 'contao_backend';

		$token = new UsernamePasswordToken($user->username,null, $firewallContext, $user->getRoles());

		//$authToken = $this->authenticationManager->authenticate($token);
        $this->tokenStorage->setToken($token);

        $this->session->set('_security_'.$firewallContext, serialize($token));
        $this->session->save();


        // Fire the login event manually
        $event = new InteractiveLoginEvent($this->requestStack->getCurrentRequest(), $token);
        $this->eventDispatcher->dispatch('security.interactive_login', $event);


        /*
         * maybe only works in secured route area
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $event = new InteractiveLoginEvent($request, $token);
        $this->container->get('event_dispatcher')->dispatch('security.interactive_login', $event);
        */
    }
	
	/**
	 * Overwrite contao password mechanism
     * @deprecated in Contao 5
	 */
	public function hookCheckCredentials()
	{
		//return self::$loginGranted;
	}
}
