<?php

namespace AppBundle\Security;

use AppBundle\Model\User;
use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GithubUserProvider implements UserProviderInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function loadUserByUsername($username)
    {
        $response = $this->client->get(sprintf('/user?access_token=%s', $username));
        $userData = $response->json();

        if (!$userData) {
            throw new \LogicException('Did not managed to get your user info from Github.');
        }

        $user = new User;
        $user->createFrom($userData);

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException();
        }

        return $user;
    }

    public function supportsClass($class)
    {
        return 'AppBundle\Model\User' === $class;
    }
} 
