<?php

namespace Comolo\SuperLoginClient\ContaoEdition\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use League\OAuth2\Client\Provider\GenericProvider as OAuth2GenericProvider;
use Comolo\SuperLoginClient\ContaoEdition\Model\SuperLoginServerModel;

class AuthorizationController extends Controller
{
    public function authorizationAction($serverId)
    {
        // Get server
        $server = $this->findServerById($serverId);
        $request = $this->get('request');
        
        // Server not found
        if (!$server) {
            throw new AccessDeniedHttpException('Unknown server');
        }
        
        // Init provider
        $provider = new OAuth2GenericProvider([
            'clientId'                => $server->public_id,
            'clientSecret'            => $server->secret,
            'urlAuthorize'            => $server->url_authorize,
            'urlAccessToken'          => $server->url_access_token,
            'urlResourceOwnerDetails' => $server->url_resource_owner_details,
        ]);
        
        // Check state
        $state = $request->query->get('state');
        $state_session = $this->get('session')->get('oauth2state');
        
        if (empty($state) || ($state !== $state_session)) {
            dump([ empty($state), ($state !== $state_session), $state_session]);
            $this->get('session')->remove('oauth2state');
            throw new AccessDeniedHttpException('Invalid state');
        }
        
        // Connect
        //try {
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $request->query->get('code')
            ]);

            // We have an access token, which we may use in authenticated
            // requests against the service provider's API.
            dump([
                $accessToken->getToken(),
                $accessToken->getRefreshToken(),
                $accessToken->getExpires(),
                ($accessToken->hasExpired() ? 'expired' : 'not expired'),
            ]);
            
            // Using the access token, we may look up details about the
            // resource owner.
            $resourceOwner = $provider->getResourceOwner($accessToken);

            dump($resourceOwner->toArray());
            dump($accessToken);
            // The provider provides a way to get an authenticated API request for
            // the service, using the access token; it returns an object conforming
            // to Psr\Http\Message\RequestInterface.
            $authRequest = $provider->getAuthenticatedRequest(
                'GET',
                $server->url_resource_owner_details,
                $accessToken
            );
            
            

        /*} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

            // Failed to get the access token or user details.
            dump($server->url_resource_owner_details);
            dump($e->getMessage());
            throw new AccessDeniedHttpException('an auth error occured');
        }
        */
        return new Response('auth:' . $serverId);
    }
    
    protected function findServerById($serverId) {
        $conn = $this->get('database_connection');
        $stmt = $conn->prepare('SELECT * FROM tl_superlogin_server WHERE id = :id LIMIT 1');
        $stmt->bindValue('id', $serverId);
        $stmt->execute();
        $result = $stmt->fetchAll();
        
        if (count($result) == 1) {
            return (object) $result[0];
        }
        
        return null;
    }
}