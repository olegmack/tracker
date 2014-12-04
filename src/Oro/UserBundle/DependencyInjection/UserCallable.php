<?php

namespace Oro\UserBundle\DependencyInjection;
use Symfony\Component\DependencyInjection\Container;

class UserCallable
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getCurrentUser()
    {
        $token = $this->container->get('security.context')->getToken();
        if (null !== $token) {
            return $token->getUser();
        }
    }
}