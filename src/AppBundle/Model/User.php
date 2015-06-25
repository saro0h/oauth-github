<?php

namespace AppBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private $username;

    private $name;

    private $email;

    private $avatarUrl;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatarUrl;
    }

    public function setAvatar($avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getPassword()
    {

    }

    public function getSalt()
    {
    }

    public function getUsername()
    {
        return $this->username;
    }
    public function eraseCredentials()
    {

    }


    public function createFrom($data)
    {
        $this->username = $data['login'];
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->avatarUrl = $data['avatar_url'];
    }
}
