<?php

namespace Comolo\SuperLoginClient\ContaoEdition\Controller;

use Comolo\SuperLoginClient\ContaoEdition\Server\ServerManager;
use Comolo\SuperLoginClient\ContaoEdition\User\RemoteUserManager;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider as OAuth2GenericProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\AsRoute;

class AuthorizationController extends AbstractController
{
    public function __construct(
        private readonly ServerManager $serverManager,
        private readonly RemoteUserManager $remoteUserManager,
    ) {
    }

    /**
     * Redirect the user to the oauth server
     * @param int $serverId
     * @param SessionInterface $session
     * @return RedirectResponse
     */
    public function redirectToOAuth(
        int $serverId, 
        SessionInterface $session
    ): RedirectResponse {
        $server = $this->serverManager->find($serverId);

        if (!$server) {
            throw new AccessDeniedHttpException('Unknown server');
        }

        $provider = $this->serverManager->createOAuth2Provider($server);
        $authorizationUrl = $provider->getAuthorizationUrl();

        // Store state in session
        $session->set('oauth2state', $provider->getState());

        // Redirect user
        return new RedirectResponse($authorizationUrl);
    }

    public function authorization(
        int $serverId,
        Request $request,
        SessionInterface $session,
    ): Response {
        $server = $this->serverManager->find($serverId);
        $state = $request->query->get('state');
        $state_session = $session->get('oauth2state');

        // Server not found
        if (!$server) {
            throw new AccessDeniedHttpException('Unknown server');
        }

        $provider = $this->serverManager->createOAuth2Provider($server);

        // Validate state
        if (empty($state) || ($state !== $state_session)) {
            $session->remove('oauth2state');
            throw new AccessDeniedHttpException('Invalid state');
        }

        try {
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $request->query->get('code')
            ]);

            // Get Resource Owner
            $resourceOwner = $provider->getResourceOwner($accessToken);
            $userDetails = $resourceOwner->toArray()['user'];

            // Simulate Contao login
            $contaoUser = $this->remoteUserManager->create($userDetails);
            $this->remoteUserManager->createOrUpdate($contaoUser);
            $this->remoteUserManager->loginAs($contaoUser);

            return $this->redirectToRoute('contao_backend');

        } catch (IdentityProviderException $e) {
            // Failed to get the access token or user details.
            throw new AccessDeniedHttpException('Invalid user credentials.');
        }
    }
}
