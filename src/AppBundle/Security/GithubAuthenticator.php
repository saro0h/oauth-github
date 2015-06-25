<?php

namespace AppBundle\Security;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;


class GithubAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    private $client;
    private $clientId;
    private $clientSecret;
    private $router;

    public function __construct(Client $client, $clientId, $clientSecret, Router $router)
    {
        $this->client = $client;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->router = $router;
    }

    public function createToken(Request $request, $providerKey)
    {
        $code = $request->query->get('code');
        $redirectUri = $this->router->generate('admin_auth', [], ROUTER::ABSOLUTE_URL);

        $response = $this->client->post(sprintf('/login/oauth/access_token?client_id=%s&client_secret=%s&code=%s&redirect_uri=%s',
            $this->clientId,
            $this->clientSecret,
            $code,
            urlencode($redirectUri)
        ));

        $res = $response->json();

        if (!isset($res['access_token'])) {
            throw new BadCredentialsException('No access_token returned by Github. Start over the process.');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $res['access_token'],
            $providerKey
        );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $accessToken = $token->getCredentials();
        $user = $userProvider->loadUserByUsername($accessToken);

        return new PreAuthenticatedToken(
            $user,
            $accessToken,
            $providerKey,
            $user->getRoles()
        );
    }

    /**
     * After Symfony calls createToken(), it will then call supportsToken() on your class
     * (and any other authentication listeners) to figure out who should handle the token.
     * This is just a way to allow several authentication mechanisms to be used for the same
     * firewall (that way, you can for instance first try to authenticate the user via a
     * certificate or an API key and fall back to a form login).
     *
     * Mostly, you just need to make sure that this method returns true for a token that
     * has been created by createToken(). Your logic should probably look exactly like this example.
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response("Authentication Failed :(", 403);
    }
} 
