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
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ContaoBackendLogin
{
    protected const SECURED_AREA = 'contao_backend';
    protected $contaoUserProvider;
    protected $session;
    protected $tokenStorage;
    protected $eventDispatcher;
    protected $requestStack;
    protected $authenticationUtils;

    /**
     * ContaoBackendLogin constructor.
     * @param ContaoUserProvider $contaoUserProvider
     * @param SessionInterface $session
     * @param ContaoFrameworkInterface $contaoFramework
     * @param TokenStorageInterface $tokenStorage
     * @param EventDispatcherInterface $eventDispatcher
     * @param RequestStack $requestStack
     * @param AuthenticationUtils $authenticationUtils
     */
    public function __construct(
        ContaoUserProvider $contaoUserProvider,
        SessionInterface $session,
        ContaoFrameworkInterface $contaoFramework,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack,
        AuthenticationUtils $authenticationUtils
    ) {
        $this->contaoUserProvider = $contaoUserProvider;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->authenticationUtils = $authenticationUtils;

        if (!$contaoFramework->isInitialized()) {
            $contaoFramework->initialize();
        }
    }

    /**
     * @param RemoteUserInterface $remoteUser
     */
    public function login(RemoteUserInterface $remoteUser): void
    {
        $user = $this->contaoUserProvider->loadUserByUsername($remoteUser->getUsername());

        $token = new UsernamePasswordToken($user, null, self::SECURED_AREA, $user->getRoles());
        $this->tokenStorage->setToken($token);

        $this->session->set('_security_' . self::SECURED_AREA, serialize($token));
        $this->session->save();

        $event = new InteractiveLoginEvent($this->requestStack->getCurrentRequest(), $token);
        $this->eventDispatcher->dispatch($event, 'security.interactive_login');
    }
}
