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
    protected const SECURED_AREA = 'contao_backend';
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
		$user = $this->contaoUserProvider->loadUserByUsername($remoteUser->getUsername());

        $this->session->set('debug_username', $user->username); // TODO

		$token = new UsernamePasswordToken($user->username,null, self::SECURED_AREA, $user->getRoles());
        $this->tokenStorage->setToken($token);

        $this->session->set('_security_'.self::SECURED_AREA, serialize($token));
        $this->session->save();

        // Fire the login event
        $event = new InteractiveLoginEvent($this->requestStack->getCurrentRequest(), $token);
        $this->eventDispatcher->dispatch('security.interactive_login', $event);
    }
}
