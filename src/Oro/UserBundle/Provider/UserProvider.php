<?php

namespace Oro\UserBundle\Provider;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return bool|UserInterface
     */
    public function getCurrentUser()
    {
        $token = $this->container->get('security.context')->getToken();
        if (null !== $token) {
            return $token->getUser();
        }

        return false;
    }
}
