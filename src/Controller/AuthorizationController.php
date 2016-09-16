<?php

namespace Comolo\SuperLoginClient\ContaoEdition\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use League\OAuth2\Client\Provider\GenericProvider as OAuth2GenericProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Comolo\SuperLoginClient\ContaoEdition\Model\SuperLoginServerModel;
use Comolo\SuperLoginClient\ContaoEdition\Foundation\User\RemoteContaoUser;
use Comolo\SuperLoginClient\ContaoEdition\Exception\InvalidUserDetailsException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthorizationController extends Controller
{
    /**
     * Redirect the user to the oauth server
     *
     */
    public function redirectAction($serverId)
    {
        $server = $this->get('superlogin.server_manager')->find($serverId);

        // Server not found
        if (!$server) {
            throw new AccessDeniedHttpException('Unknown server');
        }

        // Create oauth client instance
        $provider = $this->get('superlogin.server_manager')->createOAuth2Provider($server);
        $authorizationUrl = $provider->getAuthorizationUrl();

        // Store state in session
        $this->get('session')->set('oauth2state', $provider->getState());

        // Redirect user
        return new RedirectResponse($authorizationUrl, 302);

    }

    public function authorizationAction($serverId)
    {
        $server = $this->get('superlogin.server_manager')->find($serverId);
        $request = $this->get('request_stack')->getCurrentRequest();
        $state = $request->query->get('state');
        $state_session = $this->get('session')->get('oauth2state');

        // Server not found
        if (!$server) {
            throw new AccessDeniedHttpException('Unknown server');
        }

        // Init provider
        $provider = $this->get('superlogin.server_manager')->createOAuth2Provider($server);

        // Validate state
        if (empty($state) || ($state !== $state_session)) {
            $this->get('session')->remove('oauth2state');
            throw new AccessDeniedHttpException('Invalid state');
        }

        try {

            // Get Access token
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $request->query->get('code')
            ]);

            // Get Resource Owner
            $resourceOwner = $provider->getResourceOwner($accessToken);
            $userDetails = $resourceOwner->toArray()['user'];

            // Simulate Contao login
            $contaoUser = $this->get('superlogin.remote_user')->create($userDetails);
            $this->get('superlogin.remote_user')->createOrUpdate($contaoUser);
            $this->get('superlogin.remote_user')->loginAs($contaoUser);

            return $this->redirectToRoute('contao_backend');

        } catch (IdentityProviderException $e) {
            // Failed to get the access token or user details.
            throw new AccessDeniedHttpException('an error occured');
        }
    }
}
